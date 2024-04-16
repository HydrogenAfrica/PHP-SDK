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

        $key = ($mode == 'test') ? $secretKey : $publicKey;
        $hdrogenInlineScript = ($mode == 'test') ? $testInlineScript : $liveInlineScript;
        $token = base64_encode($key);


        $this->logger->info('Rendering Payment Modal..');
        $html = '';
        $html .= '<!DOCTYPE html>';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<meta name="viewport" content="width=device-width, initial-scale=1" />';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<script src="' . $hdrogenInlineScript . '"></script>';
        $html .= '<script>';
        $html .= 'let obj = {';
        $html .= '"amount": "' . $payload['amount'] . '",';
        $html .= '"email": "' . $payload['email'] . '",';
        $html .= '"currency": "' . $currency . '",';
        $html .= '"description": "' . $payload['description'] . '",';
        $html .= '"meta": "' . $payload['meta'] . '",';
        $html .= '"callback":"' . $payload['callback'] . '",';
        $html .= '"customerName":"' . $payload['customerName'] . '",';
        $html .= '"isAPI": false,';
        $html .= '};';
        $html .= 'let token = atob("' . $token . '");';
        $html .= 'async function openDialogModal() {';
        $html .= 'try {';
        $html .= 'let res = await handlePgData(obj, token);';
        $html .= 'console.log("return transaction ref", res);';
        $html .= 'if (window.innerWidth > 768) {';
        $html .= 'let a = document.getElementById("modal");';
        $html .= 'a.style.height = "95%";';
        $html .= 'let t = document.getElementById("myModal");';
        $html .= 't.style.paddingTop = "1%";';
        $html .= 't.style.paddingBottom = "0%";';
        $html .= 't.style.zIndex = "9999";';
        $html .= 'let n = document.querySelector(".pgIframe");';
        $html .= 'n.style.width = "27rem";';
        $html .= '} else {';
        $html .= 'let a = document.getElementById("modal");';
        $html .= 'a.style.height = "80%";';
        $html .= 'a.style.zIndex = "9";';
        $html .= 'a.style.marginTop = "40px";';
        $html .= 'a.style.marginBottom = "40px";';
        $html .= '}';
        $html .= '} catch (error) {';
        $html .= 'console.error("Error occurred:", error);';
        $html .= '}';
        $html .= '}';
        $html .= 'openDialogModal();'; // Immediately open the modal
        $html .= '</script>';
        $html .= '</body>';
        $html .= '</html>';
        $this->logger->info('Rendered Payment Modal Successfully..');
        return $html;
    }
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
