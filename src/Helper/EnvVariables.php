<?php

declare(strict_types=1);

namespace HydrogenpayAfrica\Helper;

class EnvVariables
{

    public const BASE_URL = 'https://qa-dev.hydrogenpay.com/qa/bepay/api/v1/merchant/initiate-payment';
    public const LIVE_URL = 'https://api.hydrogenpay.com/bepay/api/v1/merchant/initiate-payment';
    public const TEST_INLINE_SCRIPT = 'https://hydrogenshared.blob.core.windows.net/paymentgateway/paymentGatewayInegration.js';
    public const LIVE_INLINE_SCRIPT = 'https://hydrogenshared.blob.core.windows.net/paymentgateway/HydrogenPGIntegration.js';
    public const VERIFY_TEST_PAY = 'https://qa-api.hydrogenpay.com/bepayment/api/v1/Merchant/confirm-payment';
    public const VERIFY_LIVE_PAY = 'https://api.hydrogenpay.com/bepay/api/v1/Merchant/confirm-payment';

    public const TIME_OUT = 30;
}
