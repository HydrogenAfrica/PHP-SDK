<?php

namespace Unit\Service;

use HydrogenpayAfrica\Config\ForkConfig;
use HydrogenpayAfrica\Controller\TransactionController;
use HydrogenpayAfrica\Contract\ConfigInterface;
use HydrogenpayAfrica\EventHandlers\EventHandlerInterface;
use HydrogenpayAfrica\EventHandlers\ModalEventHandler;
use HydrogenpayAfrica\HydrogenpayAfrica;
use HydrogenpayAfrica\Library\Modal;
use PHPUnit\Framework\TestCase;

class TransactionProcessTest extends TestCase 
{
    protected HydrogenpayAfrica $paymentClient;

    protected function setUp(): void
    {
        HydrogenpayAfrica::bootstrap();

        // $this->paymentHandler = new ModalEventHandler();
    }

    /**
     * Tests the transaction process for different modal types (REDIRECT / POPUP).
     * @dataProvider transactionProvider
     * @test
     */
    public function TransactionProcess(
        string $modalType,
        array $generatedTransactionData, 
        EventHandlerInterface $paymentHandler, 
        ConfigInterface $config,
        array $request
    ){
        // Create mock objects
        $mockClient = $this->createMock(HydrogenpayAfrica::class);
        $mockModal = $this->createMock(Modal::class);

        // Setup expectations for the mock objects
        $mockModal
            ->expects($this->exactly(1))
            ->method('with')
            ->will($this->returnValue($mockModal));

        if( 'redirect' === $modalType ) {
            $mockModal
                ->expects($this->exactly(1))
                ->method('getUrl')
                ->will($this->returnValue(''));
        } else {
            $mockModal
                ->expects($this->exactly(1))
                ->method('getHtml')
                ->will($this->returnValue(''));
        }

        $mockClient
            ->expects($this->exactly(1))
            ->method('render')
            ->with( $modalType )
            ->will($this->returnValue($mockModal));

        $mockClient
            ->expects($this->exactly(1))
            ->method('eventHandler')
            ->will($this->returnValue($mockClient));

        $_SERVER['REQUEST_METHOD'] = 'POST';

        // Create an instance of the TransactionController and call the process method
        $controller = new TransactionController( $mockClient , $paymentHandler, $modalType );
        $controller->process( $request );
    }

    /**
     * Provides data for the transaction process test.
     */
    public function transactionProvider() {
        return [
            [
                Modal::REDIRECT,
                [ "TransactionRef" => 'HY_TEST|' . random_int( 10, 2000) . '|' . uniqid('aMx') ],
                new ModalEventHandler(),
                ForkConfig::setUp(
                    $_ENV['TEST_AUTH_TOKEN'],
                    $_ENV['LIVE_AUTH_TOKEN'],
                    $_ENV['MODE']
                ),
                [
                    'amount' => 100,
                    'email' => 'bwitlawalyusuf@gmail.com',
                    'currency' => 'NGN',
                    'description' => 'Payment Process sdk test',
                    'customerName' => 'Yusuf Lawal',
                    'meta' => 'SDK Unit test',
                    'callback' => 'https:hydrogenpay.com',
                    'success_url' => null,
                    'failure_url' => null,
                ]
            ],
            [
                Modal::POPUP,
                [ "TransactionRef" => 'HY_TEST|' . random_int( 10, 2000) . '|' . uniqid('mAx') ],
                new ModalEventHandler(),
                ForkConfig::setUp(
                    $_ENV['TEST_AUTH_TOKEN'],
                    $_ENV['LIVE_AUTH_TOKEN'],
                    $_ENV['MODE']
                ),
                [
                    'amount' => 100,
                    'email' => 'bwitlawalyusuf@gmail.com',
                    'currency' => 'NGN',
                    'description' => 'Payment Process sdk test',
                    'customerName' => 'Owolabi',
                    'meta' => 'SDK Unit test',
                    'callback' => 'https:hydrogenpay.com',
                    'success_url' => null,
                    'failure_url' => null,
                ]
            ]
        ];
    }

}