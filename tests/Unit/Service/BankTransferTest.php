<?php

namespace Unit\Service;

use HydrogenpayAfrica\Test\Resources\Setup\Config;
use HydrogenpayAfrica\HydrogenpayAfrica;
use HydrogenpayAfrica\Util\AuthMode;
use PHPUnit\Framework\TestCase;
use HydrogenpayAfrica\Util\Currency;


class BankTransferTest extends TestCase
{
    protected function setUp(): void
    {
        HydrogenpayAfrica::bootstrap();
    }

    public function testAuthModeReturnBankTransfer()
    {
        $data = [
            "amount" => 2000,
            "currency" => Currency::NGN,
            "tx_ref" => uniqid().time(),
            "redirectUrl" => "https://google.com"
        ];

        $btpayment = HydrogenpayAfrica::create("bank-transfer");
        $customerObj = $btpayment->customer->create([
            "full_name" => "Lawal Yusuf",
            "email" => "developers@hydrogenpay.com",
            "phone" => "+2347035579326"
        ]);

        $data['customer'] = $customerObj;
        $payload  = $btpayment->payload->create($data);
        $result = $btpayment->initiate($payload);
        $this->assertSame(AuthMode::BANKTRANSFER, $result['mode']);
    }


    public function testExpiryOption()
    {
        $data = [
            "amount" => 2000,
            "currency" => Currency::NGN,
            "tx_ref" => uniqid().time(),
            "redirectUrl" => "https://google.com",
            "expires" => 3600
        ];

        $btpayment = HydrogenpayAfrica::create("bank-transfer");
        $customerObj = $btpayment->customer->create([
            "full_name" => "Yusuf Lawal",
            "email" => "developers@hydrogenpay.com",
            "phone" => "+2347035579326"
        ]);

        $data['customer'] = $customerObj;
        $payload  = $btpayment->payload->create($data);
        $result = $btpayment->initiate($payload);
        $this->assertTrue(isset($result['account_expiration']));
    }
}