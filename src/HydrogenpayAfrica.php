<?php

declare(strict_types=1);

namespace HydrogenpayAfrica;

use HydrogenpayAfrica\Config\ForkConfig;
use HydrogenpayAfrica\EventHandlers\EventHandlerInterface;
use HydrogenpayAfrica\Exception\ApiException;
use HydrogenpayAfrica\Helper\CheckCompatibility;
use HydrogenpayAfrica\Traits\PaymentFactory;
use HydrogenpayAfrica\Traits\Setup\Configure;
use HydrogenpayAfrica\Library\Modal;
use Psr\Http\Client\ClientExceptionInterface;

define('HY_PHP_ASSET_DIR', __DIR__ . '/../assets/');

/**
 * Hydrogenpay PHP SDK
 *
 * @author HydrogenpayAfrica Developers <developers@hydrogenpay.com>
 *
 * @version 1.0
 */
class HydrogenpayAfrica extends AbstractPayment
{
    use Configure;
    use PaymentFactory;

    /**
     * HydrogenpayAfrica Construct
     *
     * @param string $prefix
     * @param bool   $overrideRefWithPrefix Set this parameter to true to use your prefix as the transaction reference
     */
    public function __construct()
    {
        parent::__construct();
        $this->logger = self::$config->getLoggerInstance();
        $this->createReferenceNumber();
        $this->logger->notice('Main Class Initializes....');
    }

    /**
     * Sets the transaction amount
     *
     * @param string $amount Transaction amount
     * */
    public function setAmount(string $amount): object
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Sets the allowed payment methods
     *
     * @param string $paymentOptions The allowed payment methods. Can be card, account or both
     */
    public function setPaymentOptions(string $paymentOptions): object
    {
        $this->paymentOptions = $paymentOptions;
        return $this;
    }

    /**
     * get event handler.
     *
     * @param string $paymentOptions The allowed payment methods. Can be card, account or both
     */
    public function getEventHandler()
    {
        return $this->handler;
    }

    /**
     * Sets the transaction description
     *
     * @param string $description The description of the transaction
     */
    public function setDescription(string $description): object
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Sets the payment page customer name
     *
     * @param string $customerName A title for the payment.
     *                            It can be the product name, your business name or anything short and descriptive
     */
    public function setTitle(string $customerName): object
    {
        $this->customerName = $customerName;
        return $this;
    }

    /**
     * Sets transaction country
     *
     * @param string $country The transaction country. Can be NG, US
     */
    public function setCountry(string $country): object
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Sets the transaction currency
     *
     * @param string $currency The transaction currency. Can be NGN, GHS, KES, ZAR, USD
     */
    public function setCurrency(string $currency): object
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Sets the customer email
     *
     * @param string $customerEmail This is the paying customer's email
     */
    public function setEmail(string $email): object
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Sets the customer firstname
     *
     * @param string $callback This is the paying customer's firstname
     */
    public function setCallback(string $callback): object
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * Sets the payment page button text
     *
     * @param string $payButtonText This is the text that should appear
     *                              on the payment button on the hydrogen payment gateway.
     */
    public function setPayButtonText(string $payButtonText): object
    {
        $this->payButtonText = $payButtonText;
        return $this;
    }

    /**
     * Sets the transaction redirect url
     *
     * @param string $redirectUrl This is where the HydrogenpayAfrica will redirect to after
     *                            completing a payment
     */
    public function setRedirectUrl(string $redirectUrl): object
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }

    /**
     * Sets the transaction meta data. Can be called multiple time to set multiple meta data
     *
     * @param array $meta This are the other information you will like to store
     *                    with the transaction. It is a key => value array. eg. PNR for airlines,
     *                    product colour or attributes. Example. array('name' => 'femi')
     */
    public function setMetaData(array $meta): object
    {
        $this->meta = [$this->meta, $meta];
        return $this;
    }

    /**
     * Sets the event hooks for all available triggers
     *
     * @param EventHandlerInterface $handler This is a class that implements the
     *                                       Event Handler Interface
     */
    public function eventHandler(EventHandlerInterface $handler): object
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * Requery initiation for a previous transaction from the hydrogen payment gateway
     *
     * @param  string $transactionRef This should be the reference number of the transaction you want to requery
     * @throws ClientExceptionInterface
     * @throws ApiException
     */
    public function requeryTransaction(string $transactionRef): object
    {
        // Transaction reference for the requery
        $this->transactionRef = $transactionRef;

        // Requery attempt log
        $this->logger->notice('Requerying Transaction....' . $this->transactionRef);

        // Call the requery handler if it is set
        if (isset($this->handler)) {
            $this->handler->onRequery($this->transactionRef);
        }

        // Data Prepare for the requery
        $data = [
            'transactionRef' => $transactionRef,
        ];

        // Sending a POST request
        $response = $this->postURL(static::$config, $data);

        // Response to determine the status of the transaction
        $transConfirmation = $responseObj = (object) [
            'status' => $response, // Paid or Failed
        ];

        // Check if the transaction was successful or failed
        if ($transConfirmation->status == 'Paid') {
            $this->logger->notice('Requeryed a successful transaction....' . $response);

            // Handle successful transaction
            if (isset($this->handler)) {
                $this->handler->onSuccessful($transConfirmation->status);
            }
        } elseif ($transConfirmation->status == 'Failed') {
            // Log failed requery
            $this->logger->warning('Requeryed a failed transaction....' . $response);

            // Handle failed transaction
            if (isset($this->handler)) {
                $this->handler->onFailure($transConfirmation->status);
            }
        }

        // Return method
        return $this;
    }


    public function initialize(): void
    {
        $this->logger->info('Rendering Payment Modal..');

        echo '<html lang="en">';
        echo '<body>';
        echo '<script type="text/javascript" src="https://hydrogenshared.blob.core.windows.net/paymentgateway/paymentGatewayIntegration_v1PROD.js"></script>';
        echo '<script>';
        echo 'document.addEventListener("DOMContentLoaded", function(event) {';
        echo 'let obj = {
        amount: ' . $this->amount . ',
        email: "' . $this->email . '",
        currency: "' . $this->currency . '",
        description: "' . $this->description . '",
        meta: "' . $this->customerName . '",
        callback: "' . $this->callback . '",
        isAPI: false,
    };';

        echo 'let token = "' . self::$config->getPublicKey() . '";';
        echo 'async function openDialogModal() {
        let res = await handlePgData(obj, token);
        console.log("return transaction ref", res);
    }';

        echo 'HydrogenpayCheckout({
        live_api_key: "' . self::$config->getPublicKey() . '",
        amount: ' . $this->amount . ',
        currency: "' . $this->currency . '",
        country: "' . $this->country . '",
        callback:"' . $this->callback . '",
        email: "' . $this->email . '",
        customerName: "' . $this->customerName . '",
        meta: "' . $this->customerName . '",
        description: "' . $this->description . '",
    });';

        echo 'openDialogModal();'; // Trigger the openDialogModal function

        echo '});';
        echo '</script>';
        echo '</body>';
        echo '</html>';

        $this->logger->info('Rendered Payment Modal Successfully..');
    }


    /**
     * Handle canceled payments with this method
     *
     * @param string $referenceNumber This should be the reference number of the transaction that was canceled
     */
    public function paymentCanceled(string $referenceNumber): object
    {
        $this->logger->notice('Payment was canceled by user..' . $referenceNumber);
        if (isset($this->handler)) {
            $this->handler->onCancel($referenceNumber);
        }
        return $this;
    }
    public static function setUp(array $config): void
    {
        self::$config = ForkConfig::setUp(
            $config['sandbox'],
            $config['live_api_key'],
            $config['mode']

        );
    }
    public function render(string $modalType): Modal
    {
        $data = [
            'transactionRef' => $this->transactionPrefix, //Tod
        ];
        return new Modal($modalType, $data, $this->getEventHandler(), self::$config);
    }
}
