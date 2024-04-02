<?php

namespace Unit\Service;

use PHPUnit\Framework\TestCase;
use HydrogenAfrica\HydrogenAfrica;
use HydrogenAfrica\Util\AuthMode;
use HydrogenAfrica\Util\Currency;
use HydrogenAfrica\Test\Resources\Setup\Config;

class AccountTest extends TestCase
{
    protected function setUp(): void
    {
        HydrogenAfrica::bootstrap();
    }

    public function testNgnAuthModeReturn()
    {
        //currently returning "Sorry, we could not connect to your bank";

        $data = [
            "amount" => 2000,
            "currency" => Currency::NGN,
            "tx_ref" => uniqid().time(),
            "additionalData" => [
                "account_details" => [
                    "account_bank" => "044",
                    "account_number" => "0690000034",
                    "country" => "NG"
                ]
            ],
        ];

        $accountpayment = \HydrogenAfrica\HydrogenAfrica::create("account");
        $customerObj = $accountpayment->customer->create([
            "full_name" => "Owolabi",
            "email" => "developers@hydrogenpay.com",
            "phone" => "+23435105142"
        ]);

        $data['customer'] = $customerObj;
        $payload  = $accountpayment->payload->create($data);
        $result = $accountpayment->initiate($payload);
        $this->assertTrue( $result['mode'] === AuthMode::REDIRECT );
    }

    public function testInvalidParam()
    {
        $data = [
            "amount" => 2000,
            "currency" => Currency::NGN,
            "tx_ref" => uniqid().time(),
            "additionalData" => null,
        ];

        $accountpayment = \HydrogenAfrica\HydrogenAfrica::create("account");
        $customerObj = $accountpayment->customer->create([
            "full_name" => "Owolabi",
            "email" => "developers@hydrogenpay.com",
            "phone" => "+23456309675"
        ]);

        $data['customer'] = $customerObj;
        $payload  = $accountpayment->payload->create($data);
        $this->expectException(\InvalidArgumentException::class);
        $result = $accountpayment->initiate($payload);
    }

    public function testUKBankAccountAuthMode() {
        $data = [
            "amount" => 2000,
            "currency" => Currency::NGN,
            "tx_ref" => uniqid().time(),
            "additionalData" => [
                "account_details" => [
                    "account_bank" => "044",
                    "account_number" => "0690000034",
                    "country" => "UK" //or EU
                ]
            ],
        ];

        $accountpayment = \HydrogenAfrica\HydrogenAfrica::create("account");
        $customerObj = $accountpayment->customer->create([
            "full_name" => "Adekunle",
            "email" => "developers@hydrogenpay.com",
            "phone" => "+23456309675"
        ]);

        $data['customer'] = $customerObj;
        $payload  = $accountpayment->payload->create($data);
        $result = $accountpayment->initiate($payload);

        $this->assertTrue( $result['mode'] === AuthMode::REDIRECT );
    }
}