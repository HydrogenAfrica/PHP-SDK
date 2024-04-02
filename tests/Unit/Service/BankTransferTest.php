<?php

namespace Unit\Service;

use HydrogenAfrica\Test\Resources\Setup\Config;
use HydrogenAfrica\HydrogenAfrica;
use HydrogenAfrica\Util\AuthMode;
use PHPUnit\Framework\TestCase;
use HydrogenAfrica\Util\Currency;


class BankTransferTest extends TestCase
{
    protected function setUp(): void
    {
        HydrogenAfrica::bootstrap();
    }

    public function testAuthModeReturnBankTransfer()
    {
        $data = [
            "amount" => 2000,
            "currency" => Currency::NGN,
            "tx_ref" => uniqid().time(),
            "redirectUrl" => "https://google.com"
        ];

        $btpayment = HydrogenAfrica::create("bank-transfer");
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

        $btpayment = HydrogenAfrica::create("bank-transfer");
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