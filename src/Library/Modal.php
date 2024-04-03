<?php

/**
 * Payment Modal.
 */

declare(strict_types=1);

namespace HydrogenAfrica\Library;

use HydrogenAfrica\EventHandlers\EventHandlerInterface;
use HydrogenAfrica\Helper\CheckoutHelper;
use HydrogenAfrica\Service\Service as Http;
use HydrogenAfrica\Entities\Payload;
use Psr\Log\LoggerInterface;
use HydrogenAfrica\Helper\EnvVariables;

final class Modal
{
    public const POPUP = 'inline';
    public const REDIRECT = 'redirect';
    private \HydrogenAfrica\Entities\Payload $payload;
    private \HydrogenAfrica\Entities\Customer $customer;
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
        $this->payload = (new \HydrogenAfrica\Factories\PayloadFactory())->create([
            'amount' => $args['amount'],
            'email' => $args['email'],
            'currency' => 'NGN',
            'description' => $args['description'],
            'customerName' => $args['customerName'],
            'meta' => $args['meta'],
            'callback' => $args['callback'],
        ]);

        $this->payload->set('redirect_url', $args['redirect_url']);

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

        $this->payload->set('payload_hash', CheckoutHelper::generateHash($dataToHash, $key));
        $this->payload->set('payload_url', CheckoutHelper::generatePayloadUrl($dataToHash, $hdrogenUrl));
        $this->payload->set('payload_inline', CheckoutHelper::generatePayloadUrl($dataToHash, $hdrogenInlineScript));
        return $this;
    }

    public function getHtml()
    {
        if ($this->type !== self::POPUP) {
            return $this->returnUrl();
        }

        $payloadArray = is_array($this->payload) ? $this->payload : $this->payload->toArray('modal');

        $html = '';
        $html .= '<html lang="en">';
        $html .= '<body>';
        $html .= '<script type="text/javascript" src="' . $payloadArray['payload_inline'] . '"></script>';
        $html .= '<script>';
        $html .= 'document.addEventListener("DOMContentLoaded", function(event) {';
        $html .= 'let token = "' . $payloadArray['payload_hash'] . '";';
        $html .= 'let obj = ' . json_encode($payloadArray) . ';';
        $html .= 'async function openDialogModal() {';
        $html .= 'let res = await handlePgData(obj, token);';
        $html .= 'console.log("return transaction ref", res);';
        $html .= '}';
        $html .= 'openDialogModal();';
        $html .= '});';
        $html .= '</script>';
        $html .= '</body>';
        $html .= '</html>';
        return $html;
    }

    public function getUrl()
    {

        if ($this->type !== self::REDIRECT) {
            return $this->returnHtml();
        }

        $payload         = $this->payload->toArray('modal');
        $response = (new Http(self::$config))->request($payload, 'POST');
        return $response->data->url;
    }
}
