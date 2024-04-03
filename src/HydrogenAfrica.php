<?php

declare(strict_types=1);

namespace HydrogenAfrica;

use HydrogenAfrica\Config\ForkConfig;
use HydrogenAfrica\EventHandlers\EventHandlerInterface;
use HydrogenAfrica\Exception\ApiException;
use HydrogenAfrica\Helper\CheckCompatibility;
use HydrogenAfrica\Traits\PaymentFactory;
use HydrogenAfrica\Traits\Setup\Configure;
use HydrogenAfrica\Library\Modal;
use Psr\Http\Client\ClientExceptionInterface;

define('FLW_PHP_ASSET_DIR', __DIR__ . '../assets/');

/**
 * Hydrogen PHP SDK
 *
 * @author HydrogenAfrica Developers <developers@hydrogenpay.com>
 *
 * @version 1.0
 */
class HydrogenAfrica extends AbstractPayment
{
    use Configure;
    use PaymentFactory;

    /**
     * HydrogenAfrica Construct
     *
     * @param string $prefix
     * @param bool   $overrideRefWithPrefix Set this parameter to true to use your prefix as the transaction reference
     */
    public function __construct()
    {
        parent::__construct();
        // $this->checkPageIsSecure(); Comment out due to env
        // create a log channel
        $this->logger = self::$config->getLoggerInstance();
        $this->createReferenceNumber();
        $this->logger->notice('Main Class Initializes....');
    }

    // private function checkPageIsSecure() Comment out due to env
    // {
    //     if(!CheckCompatibility::isSsl() && 'production' === $this->getConfig()->getEnv()) {

    //         throw new \Exception('HydrogenAfrica: cannot load checkout modal on an unsecure page - no SSL detected. ');
    //     }
    // }

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
     * @param string $customDescription The description of the transaction
     */
    public function setDescription(string $customDescription): object
    {
        $this->customDescription = $customDescription;
        return $this;
    }

    /**
     * Sets the payment page logo
     *
     * @param string $customLogo Your Logo
     */
    public function setLogo(string $customLogo): object
    {
        $this->customLogo = $customLogo;
        return $this;
    }

    /**
     * Sets the payment page title
     *
     * @param string $customTitle A title for the payment.
     *                            It can be the product name, your business name or anything short and descriptive
     */
    public function setTitle(string $customTitle): object
    {
        $this->customTitle = $customTitle;
        return $this;
    }

    /**
     * Sets transaction country
     *
     * @param string $country The transaction country. Can be NG, US, KE, GH and ZA
     */
    public function setCountry(string $country): object
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Sets the transaction currency
     *
     * @param string $currency The transaction currency. Can be NGN, GHS, KES, ZAR, USD, EUR and GBP
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
    public function setEmail(string $customerEmail): object
    {
        $this->customerEmail = $customerEmail;
        return $this;
    }

    /**
     * Sets the customer firstname
     *
     * @param string $customerFirstname This is the paying customer's firstname
     */
    public function setFirstname(string $customerFirstname): object
    {
        $this->customerFirstname = $customerFirstname;
        return $this;
    }

    /**
     * Sets the customer lastname
     *
     * @param string $customerLastname This is the paying customer's lastname
     */
    public function setLastname(string $customerLastname): object
    {
        $this->customerLastname = $customerLastname;
        return $this;
    }

    /**
     * Sets the customer phonenumber
     *
     * @param string $customerPhone This is the paying customer's phonenumber
     */
    public function setPhoneNumber(string $customerPhone): object
    {
        $this->customerPhone = $customerPhone;
        return $this;
    }

    /**
     * Sets the payment page button text
     *
     * @param string $payButtonText This is the text that should appear
     *                              on the payment button on the Rave payment gateway.
     */
    public function setPayButtonText(string $payButtonText): object
    {
        $this->payButtonText = $payButtonText;
        return $this;
    }

    /**
     * Sets the transaction redirect url
     *
     * @param string $redirectUrl This is where the HydrogenAfrica will redirect to after
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
     * Requerys a previous transaction from the Rave payment gateway
     *
     * @param  string $transactionRef This should be the reference number of the transaction you want to requery
     * @throws ClientExceptionInterface
     * @throws ApiException
     */
    public function requeryTransaction(string $transactionRef): object
    {
        $this->transactionRef = $transactionRef;
        $this->requeryCount++;
        $this->logger->notice('Requerying Transaction....' . $this->transactionRef);
        if (isset($this->handler)) {
            $this->handler->onRequery($this->transactionRef);
        }

        $data = [
            // 'id' => (int) $transactionRef,
            'transactionRef' => $transactionRef,
        ];

        $url = '/transactions/' . $data['transactionRef'] . '/verify';

        $response = $this->postURL(static::$config, $data);

        //check the status is Paid of Failed.
        // if ($response->status === 'success') {

        $test = $responseObj = (object) [
            'status' => $response, // Assuming 'Paid' or 'Failed' is the status
            'data' => [
                'amount' => 100.00, // Example additional data
                'currency' => 'USD'
            ]
        ];

        if ($test->status == 'Paid') {

            $this->logger->notice('Requeryed a successful transaction....' . $response);
            // Handle successful.
            if (isset($this->handler)) {
                $this->handler->onSuccessful($test->status);
            }
        } else { // Use elseif instead of else
            // Handle Failure

            $this->logger->warning('Requeryed a failed transaction....' . $response);

            if (isset($this->handler)) {
                $this->handler->onFailure($test->status);
            }
        }

        return $this;
    }

    public function initialize(): void
    {
        $this->createCheckSum();

        $this->logger->info('Rendering Payment Modal..');

        echo '<html lang="en">';
        echo '<body>';
        echo '<div style="display: flex; flex-direction: row;justify-content: center; align-content: center ">
    Proccessing...<img src="../assets/images/ajax-loader.gif"  alt="loading-gif"/></div>';
        echo '<script type="text/javascript" src="https://hydrogenshared.blob.core.windows.net/paymentgateway/paymentGatewayInegration.js"></script>';
        echo '<script>';
        echo 'document.addEventListener("DOMContentLoaded", function(event) {';
        echo 'let obj = {
        amount: ' . $this->amount . ',
        email: "' . $this->customerEmail . '",
        currency: "' . $this->currency . '",
        description: "' . $this->customDescription . '",
        meta: "' . $this->customTitle . '",
        callback: "' . $this->redirectUrl . '",
        isAPI: false,
    };';

        echo 'let token = "' . self::$config->getPublicKey() . '";';
        echo 'async function openDialogModal() {
        let res = await handlePgData(obj, token);
        console.log("return transaction ref", res);
    }';

        echo 'HydrogenCheckout({
        live_auth_token: "' . self::$config->getPublicKey() . '",
        amount: ' . $this->amount . ',
        currency: "' . $this->currency . '",
        country: "' . $this->country . '",
        callback:"' . $this->redirectUrl . '",
        email: "' . $this->customerEmail . '",
        customerName: "' . $this->customerFirstname . ' ' . $this->customerLastname . '",
        meta: "' . $this->customTitle . '",
        description: "' . $this->customDescription . '",
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
            $config['test_auth_token'],
            $config['live_auth_token'],
            $config['mode']

        );
    }

    public function render(string $modalType): Modal
    {
        $data = [
            'tx_ref' => $this->txref,
        ];
        return new Modal($modalType, $data, $this->getEventHandler(), self::$config);
    }
}
