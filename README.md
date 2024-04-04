Hydrogen PHP SDK (Including Frameworks - Laravel|Symfomy|Codeigniter)

**Integrating Hydrogen PHP SDK Library for easy access to Hydrogen APIs**
=========================================================================

![Packagist Downloads](https://img.shields.io/packagist/dt/hydrogenpay/hydrogenpay-sdk)

![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/hydrogenpay/hydrogenpay-sdk)

![Packagist License](https://img.shields.io/packagist/l/hydrogenpay/hydrogenpay-sdk)

**Introduction**
================

**Hydrogen Payment Gateway helps you process payments using cards and account transfers for faster delivery of goods and services on your PHP site.PHP Library provides easy access to Hydrogen APIs from php apps. It abstracts the complexity involved in direct integration and allows you to make quick calls to the APIs.This SDK communicates with Hydrogen API. You need to have a Hydrogen merchant account and Auth Key to use this SDK.**

**Requirements**
================

*   **Composer**
    
*   **An IDE**
    
*   **Hydrogen Auth Token**
    
*   **Acceptable PHP versions: >= 7.4.0. for older versions of PHP**
    

**Sign up account here:** [**https://dashboard.hydrogenpay.com/signup**](https://dashboard.hydrogenpay.com/signup) 


**Installation** 
=========================================================================

**To get started, First you need to install the package into your existing project.**

**To install the package via Composer, run the following command.**

```shell**

composer require hydrogenpay/hydrogenpay-sdk

```

**Alternatively, you can add the package to your composer.json file and run the command composer install on your editor terminal.**

```json

 {

    "require": {

        "hydrogenpay/hydrogenpay-sdk": "^1.0"

    }

}

```

**This command installs the package. The package can be found in the vendor folder.if you get an error message while running the command, ensure you have composer installed.**


**Initialization** 
===================

**After installation, create a .env file in the root of the project. Most frameworks (such as Laravel and Symfony support the use of .env files). if you are not using a framework you need to create one.**

**Create a .env file and follow the format of the .env.example file.Save your, TEST\_AUTH\_TOKEN, LIVE\_AUTH\_TOKEN, MODE  in the .env fileYour .env file should look like the below:**

```env

TEST\_AUTH\_TOKEN=XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

LIVE\_AUTH\_TOKEN=XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

MODE=test

# MODE=live // test mode for payment testing and live mode is for production

```

**The SDK provides two easy methods of making collections via the Payment Gateway modal.** 

*   **REDIRECT**
    
*   **POPUP**
    

```php

try {

    Flutterwave::bootstrap();

    $customHandler = new PaymentHandler();

    $client = new Flutterwave();

    $modalType = Modal::POPUP; // Modal::POPUP or Modal::STANDARD

    $controller = new PaymentController( $client, $customHandler, $modalType );

} catch(\\Exception $e ) {

    echo $e->getMessage();

}

```

**Edit the processTransaction.php files to suite your purpose by either changing the Modal to POPUP or REDIRECT.**


**Transaction Resources** 
=========================================================================

**Edit the transactionForm.php and processTransaction.php files to suit your purpose. Both files are well documented.**

**Simply redirect to the transactionForm.php file on your browser to process a payment.**

**In this implementation, we are expecting a form-encoded POST request.The request will contain the following parameters. Request parameters**

# Request Parameters

| Mandatory | Name        | Comment                                               |
|-----------|-------------|-------------------------------------------------------|
| Yes       | amount      | The amount to be charged for the transaction.         |
| Yes       | email       | The customer's email address.                         |
| Yes       | currency    | The currency in which the transaction is processed.   |
| No        | description | A brief description of the transaction.               |
| No        | meta        | Additional metadata or information related to the transaction. |
| Yes       | callback    | Callback redirection


```json

 {

   "amount": 50,

  "customerName": "Dev Test",

  "email": "devtest@randomuser.com",

  "currency": "NGN",

  "description": "test desc",

  "meta": "test meta",

  "callback": "https://hydrogenpay.com",

 }

```

**The script in processTransaction.php handles the request data via the TransactionController. If you are using a Framework like Laravel or CodeIgniter you might want to take a look at the TransactionController.** 



```php

declare(strict\_types=1);

namespace HydrogenpayAfrica\\Controller;

use HydrogenpayAfrica\\EventHandlers\\ModalEventHandler;

use HydrogenpayAfrica\\EventHandlers\\EventHandlerInterface;

use HydrogenpayAfrica\\HydrogenpayAfrica;

use HydrogenpayAfrica\\Entities\\Payload;

use HydrogenpayAfrica\\Library\\Modal;

use HydrogenpayAfrica\\Service\\Transactions;

final class TransactionController

{

    private string $requestMethod;

    private EventHandlerInterface $handler;

    private HydrogenpayAfrica $client;

    private string $modalType;

    protected array $routes = [

        'process' => 'POST',

        'callback' => 'GET'

    ];

    public function __construct(

        HydrogenpayAfrica $client,

        EventHandlerInterface $handler,

        string $modalType

    ) {

        HydrogenpayAfrica::bootstrap();

        $this->requestMethod =  $this->getRequestMethod();

        $this->handler = $handler;

        $this->client = $client;

        $this->modalType = $modalType;

    }

    private function getRequestMethod(): string

    {

        return ($_SERVER["REQUEST\_METHOD"] === "POST") ? 'POST' : 'GET';

    }

    public function __call(string $name, array $args)

    {

        if ($this->routes[$name] !== $this->$requestMethod) {

            echo "Unauthorized page!";

        }

        call_user_method_array($name, $this, $args);

    }

    private function handleSessionData(array $request)

    {

        $_SESSION['success_url'] = $request['success_url'];

        $_SESSION['failure_url'] = $request['failure_url'];

        $_SESSION['amount'] = $request['amount'];

    }

    public function process(array $request)

    {

        $this->handleSessionData($request);

        try {

            $_SESSION['p'] = $this->client;

            if ('inline' === $this->modalType) {

                echo $this->client

                    ->eventHandler($this->handler)

                    ->render(Modal::POPUP)->with($request)->getHtml();

            } else {

                $paymentLink = $this->client

                    ->eventHandler($this->handler)

                    ->render(Modal::REDIRECT)->with($request)->getUrl();

                header('Location: ' . $paymentLink);

            }

        } catch (\Exception $e) {

            echo $e->getMessage();

        }

    }

    public function callback(array $request)

    {

        $transactionRef = $request['TransactionRef'];

        if (empty($transactionRef)) {

            session_destroy();

        }

        if (!isset($_SESSION['p'])) {

            echo "session expired!. please refresh you browser.";

            exit();

        }

        $payment = $_SESSION\['p'];

        $payment::bootstrap();

        if (isset($request['TransactionRef'])) {

            $transactionRef = $request\['TransactionRef'\];

            $payment->logger->notice('Payment completed. Now requerying payment.');

            $payment

                ->eventHandler($this->handler)****

                ->requeryTransaction($transactionRef);

        }

    }

}

```


**Verifying Transaction**
=========================

```php

$request = $_GET;
# Confirming Payment.
if(isset($request['TransactionRef'])) {
    $controller->callback( $request );
} else {
    
}
exit();

```

```html
<input type="hidden" name="callback" value="http://hydrogenpay_php_sdk.test/processTransaction.php">

<input type="hidden" name="success_url" value="https://hydrogenpay.com">

<input type="hidden" name="failure_url" value="https://docs.hydrogenpay.com/docs/getting-started">

```
                   

**In transactionFrom.php, you can pass your desired URL for your project in the value:**

*   **Callback: Redirect URL after payment has been completed on the gateway.**
    
*   **Success_url : Redirect URL for Payment Success**
    
*   **Failure_url : Redirect URL for Payment Failed**
    




**Features**
============

*   **Accept payment via Mastercard, Visa, Verve, and Bank Account.**
    
*   **Seamless integration into your site checkout page. Accept payment directly on your site.**

    

**License**
==============

*The MIT License (MIT). Please see License file for more information.*


**Hydrogenpay Api References**
===============================

 - [Hydrogenpay API Documentation](https://docs.hydrogenpay.com/reference/api-authentication)

 - [Hydrogenpay Dashboard](https://dashboard.hydrogenpay.com/merchant/profile/api-integration)