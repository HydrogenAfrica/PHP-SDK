<?php

declare(strict_types=1);

namespace HydrogenpayAfrica\Helper;

final class TransactionHelper
{
    /**
     * Generate Payment Hash.
     *
     * @param array       $payload    the payload.
     * @param string|null $sandbox the secret key.
     * @param string|null $live_api_key the secret key.
     * 

     *
     * @return string
     */

    //  public static function generateHash(array $payload, ?string $secret_key = null, ?string $public_key = null): string
    public static function generateHash(array $payload, ?string $key = null): string
    {
        return $key;
    }

    public static function generatePayloadUrl(array $payload, ?string $hdrogenUrl = null): string
    {
        return $hdrogenUrl;
    }

    public static function generatePayloadInlineScript(array $payload, ?string $hdrogenInlineScript = null): string
    {
        return $hdrogenInlineScript;
    }

    /**
     * Default Payment Methods.
     *
     * @return string
     */
    public static function getDefaultPaymentOptions(): string
    {
        $methods = [
            'account',
            'banktransfer',
            'card',
            'ussd', 
        ];
        
        return implode(',', $methods);
    }

    /**
     * Get Supported Country.
     *
     * @return array
     */
    public static function getSupportedCountry(?string $currency = null): string
    {
        $baseCurrency = 'NGN';
        $countriesMap = array(
            'NGN' => 'NG',
            'USD' => 'US',
        );

        if (!is_null($currency)) {
            if (! isset($countriesMap[$currency])) {
                throw new \InvalidArgumentException("The currency $currency is not supported at checkout.");   
            }
            return $countriesMap[$currency];
        }

        return $countriesMap[$baseCurrency];
    }
}
