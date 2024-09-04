<?php

/**
 * Payment Modal.
 */

declare(strict_types=1);

namespace HydrogenpayAfrica\Library;

use HydrogenpayAfrica\EventHandlers\EventHandlerInterface;
use HydrogenpayAfrica\Helper\TransactionHelper;
use HydrogenpayAfrica\Service\Service as Http;
use HydrogenpayAfrica\Entities\Payload;
use Psr\Log\LoggerInterface;
use HydrogenpayAfrica\Helper\EnvVariables;

final class Modal
{
    public const POPUP = 'inline';
    public const REDIRECT = 'redirect';
    private \HydrogenpayAfrica\Entities\Payload $payload;
    private \HydrogenpayAfrica\Entities\Customer $customer;
    private string $type;
    private EventHandlerInterface $paymentHandler;
    private array $generatedTransactionData;

    private static object $config;
    private LoggerInterface $logger;

    private array $functions = [
        'with' => 'with',
        'getHtml' => 'getHtml',
        'getUrl' => 'getUrl'
    ];

    public function __construct(
        string $type,
        array $generatedTransactionData,
        EventHandlerInterface $paymentHandler,
        $config
    ) {
        if ($type !== self::POPUP && $type !== self::REDIRECT) {
            $type = self::REDIRECT;
        }

        $this->type = $type;
        $this->generatedTransactionData = $generatedTransactionData;
        $this->paymentHandler = $paymentHandler;
        self::$config = $config;
        $this->logger = self::$config->getLoggerInstance();
    }
    public function with(array $args)
    {
        $this->payload = (new \HydrogenpayAfrica\Factories\PayloadFactory())->create([
            'amount' => $args['amount'],
            'email' => $args['email'],
            'currency' => 'NGN',
            'description' => $args['description'],
            'customerName' => $args['customerName'],
            'meta' => $args['meta'],
            'callback' => $args['callback'],
        ]);

        $dataToHash = [
            'amount' => $args['amount'],
            'currency' => 'NGN',
            'email' => $args['email'],
        ];

        $mode = self::$config->getMode();
        $secretKey = self::$config->getSecretKey();
        $publicKey = self::$config->getPublicKey();
        $liveUrl = EnvVariables::LIVE_URL . '/';
        $testUrl = EnvVariables::BASE_URL . '/';
        $testInlineScript = EnvVariables::TEST_INLINE_SCRIPT;
        $liveInlineScript = EnvVariables::LIVE_INLINE_SCRIPT;

        $key = ($mode == 'test') ? $secretKey : $publicKey;

        $hdrogenUrl = ($mode == 'test') ? $testUrl : $liveUrl;

        $hdrogenInlineScript = ($mode == 'test') ? $testInlineScript : $liveInlineScript;

        $this->payload->set('payload_hash', TransactionHelper::generateHash($dataToHash, $key));
        $this->payload->set('payload_url', TransactionHelper::generatePayloadUrl($dataToHash, $hdrogenUrl));
        $this->payload->set('payload_inline', TransactionHelper::generatePayloadUrl($dataToHash, $hdrogenInlineScript));
        return $this;
    }

    public function getHtml()
    {
        if ($this->type !== self::POPUP) {
            return $this->getUrl();
        }
    
        $payload = $this->payload->toArray('modal');
        $currency = $payload['currency'];
    
        $mode = self::$config->getMode();
        $secretKey = self::$config->getSecretKey();
        $publicKey = self::$config->getPublicKey();
        $testInlineScript = EnvVariables::TEST_INLINE_SCRIPT;
        $liveInlineScript = EnvVariables::LIVE_INLINE_SCRIPT;
    
        $key = ($mode === 'test') ? $secretKey : $publicKey;
        $hdrogenInlineScript = ($mode === 'test') ? $testInlineScript : $liveInlineScript;
        $token = base64_encode($key);
    
        $this->logger->info('Rendering Payment Modal..');
        
        $html = '';
        $html .= '<!DOCTYPE html>';
        $html .= '<html lang="en">';
        $html .= '<head>';
        $html .= '<meta name="viewport" content="width=device-width, initial-scale=1" />';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<script src="' . $hdrogenInlineScript . '"></script>';
        $html .= '<script>';
        $html .= 'let paymentObject = {';
        $html .= '"amount": "' . $payload['amount'] . '",';
        $html .= '"email": "' . $payload['email'] . '",';
        $html .= '"currency": "' . $currency . '",';
        $html .= '"description": "' . $payload['description'] . '",';
        $html .= '"meta": "' . $payload['meta'] . '",';
        $html .= '"callback": "' . $payload['callback'] . '",';
        $html .= '"customerName": "' . $payload['customerName'] . '",';
        $html .= '"isAPI": false,';
        $html .= '};';
        $html .= 'let token = atob("' . $token . '");';
    
        // Define onClose function
        $html .= 'function onClose(e) {';
        $html .= 'var response = { event: "close", e };';
        $html .= 'window.parent.postMessage(JSON.stringify(response), "*");';
        $html .= '}';
    
        // Define onSuccess function
        $html .= 'function onSuccess(e) {';
        $html .= 'var response = { event: "success", e };';
        $html .= 'window.parent.postMessage(JSON.stringify(response), "*");';
        $html .= '}';
    
        // Define openDialogModal function
        $html .= 'async function openDialogModal(token) {';
        $html .= 'try {';
        $html .= 'let paymentResponse = await handlePgData(paymentObject, token, onClose);';
        $html .= 'console.log("Return transaction ref:", paymentResponse);';
    
        // Periodically check payment status
        $html .= 'let checkStatus = setInterval(async function() {';
        $html .= 'try {';
        $html .= 'const checkPaymentStatus = await handlePaymentStatus(paymentResponse, token);';
        $html .= 'console.log("Return checkPaymentStatus:", checkPaymentStatus);';
    
        // If the payment is successful, handle it and clear the interval
        $html .= 'if (checkPaymentStatus.status === "Paid") {';
        $html .= 'onSuccess(checkPaymentStatus);';
        $html .= 'clearInterval(checkStatus);';
        $html .= '}';
        $html .= '} catch (error) {';
        $html .= 'console.error("Error while checking payment status:", error);';
        $html .= 'clearInterval(checkStatus);';
        $html .= '}';
        $html .= '}, 1000);';
        $html .= '} catch (error) {';
        $html .= 'console.error("Error during payment processing:", error);';
        $html .= '}';
        $html .= '}';
    
        // Trigger the openDialogModal function
        $html .= 'openDialogModal(token);';
    
        // Event listener for message handling
        $html .= 'window.addEventListener("message", function(event) {';
        $html .= 'var messageResponse = JSON.parse(event.data);';
        $html .= 'switch (messageResponse.event) {';

         // Handle success case
        $html .= 'case "success":';
        $html .= 'console.log("Payment successful:", messageResponse.e);';
        $html .= 'console.log("Payment successful TranRef:", messageResponse.e.transactionRef);';

        $html .= 'var transactionRef = messageResponse.e.transactionRef;';
        $html .= 'if (transactionRef) {';
        $html .= 'var separator = window.parent.location.href.includes("?") ? "&" : "?";';
        $html .= 'window.parent.location.href += separator + "TransactionRef=" + transactionRef;';
        $html .= '}';
        $html .= 'break;';

        // Handle close case
        $html .= 'case "close":';
        $html .= 'console.log("Payment closed:", messageResponse.e);';
        $this->logger->warning('Requeryed a failed transaction....');

        $html .= 'var transactionRef = messageResponse.e;';
        $html .= 'if (transactionRef) {';
        $html .= 'var separator = window.parent.location.href.includes("?") ? "&" : "?";';
        $html .= 'window.parent.location.href += separator + "TransactionRef=" + transactionRef;';
        $html .= '}';
        $html .= 'break;';
    
        // Default case
        $html .= 'default:';
        $html .= 'console.log("Unknown event:", messageResponse);';
        $html .= 'break;';
        $html .= '}';
        $html .= '}, false);';
    
        $html .= '</script>';
        $html .= '</body>';
        $html .= '</html>';
        
        $this->logger->info('Rendered Payment Modal Successfully..');
        return $html;
    }


    //
    public function getUrl()
    {

        if ($this->type !== self::REDIRECT) {
            return $this->getHtml();
        }

        $payload         = $this->payload->toArray('modal');
        $response = (new Http(self::$config))->request($payload, 'POST');
        return $response->data->url;
    }
}
