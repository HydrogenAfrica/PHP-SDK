<?php

declare(strict_types=1);

namespace HydrogenpayAfrica\Controller;

use HydrogenpayAfrica\EventHandlers\ModalEventHandler;
use HydrogenpayAfrica\EventHandlers\EventHandlerInterface;
use HydrogenpayAfrica\HydrogenpayAfrica;
use HydrogenpayAfrica\Entities\Payload;
use HydrogenpayAfrica\Library\Modal;
use HydrogenpayAfrica\Service\Transactions;

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
        $this->requestMethod =  $this->getRequestMethod();
        $this->handler = $handler;
        $this->client = $client;
        $this->modalType = $modalType;
    }

    private function getRequestMethod(): string
    {
        return ($_SERVER["REQUEST_METHOD"] === "POST") ? 'POST' : 'GET';
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

        $payment = $_SESSION['p'];

        $payment::bootstrap();

        if (isset($request['TransactionRef'])) {
            $transactionRef = $request['TransactionRef'];

            $payment->logger->notice('Payment completed. Now requerying payment.');

            $payment
                ->eventHandler($this->handler)
                ->requeryTransaction($transactionRef);
        }
    }
}
