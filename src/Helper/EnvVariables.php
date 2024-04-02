<?php

declare(strict_types=1);

 namespace HydrogenAfrica\Helper;
 class EnvVariables 
 {

 public const BASE_URL = 'https://qa-dev.hydrogenpay.com/qa/bepay/api/v1/merchant/initiate-payment';
 public const LIVE_URL = 'https://api.hydrogenpay.com/bepay/api/v1/merchant/initiate-payment';
 public const TEST_INLINE_SCRIPT = 'https://hydrogenshared.blob.core.windows.net/paymentgateway/paymentGatewayInegration.js';
 public const LIVE_INLINE_SCRIPT = 'https://hydrogenshared.blob.core.windows.net/paymentgateway/HydrogenPGIntegration.js';

public const TIME_OUT = 30;

 }

