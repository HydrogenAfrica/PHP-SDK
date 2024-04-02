<?php

declare(strict_types=1);

namespace HydrogenAfrica\Helper;

final class CheckoutHelper
{
    /**
     * Generate Payment Hash.
     *
     * @param array       $payload    the payload.
     * @param string|null $test_auth_token the secret key.
     * @param string|null $live_auth_token the secret key.
     * 

     *
     * @return string
     */

    //  public static function generateHash(array $payload, ?string $secret_key = null, ?string $public_key = null): string
    public static function generateHash(array $payload, ?string $key = null): string
    {
        // Output the value of $secret_key for debugging
        // var_dump($key);
        // exit(); // Exit the script

        // $string_to_hash = '';
        // foreach ($payload as $value) {
        //         $string_to_hash .= $value;
        // }

        // $string_to_hash .= hash('sha256', $key);
        // var_dump($string_to_hash);
        // return hash('sha256',$string_to_hash);
        return $key;

    }

    public static function generatePayloadUrl(array $payload, ?string $hdrogenUrl = null): string
    {
        // var_dump($hdrogenUrl);
        return $hdrogenUrl;
    }

    public static function generatePayloadInlineScript(array $payload, ?string $hdrogenInlineScript = null): string
    {
        // var_dump($hdrogenUrl);
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
        $baseCurrency = 'NGN'; // TODO: allow users to set base currency.
        $countriesMap = array(
            'NGN' => 'NG',
            'EUR' => 'NG',
            'GBP' => 'NG',
            'USD' => 'US',
            'KES' => 'KE',
            'ZAR' => 'ZA',
            'TZS' => 'TZ',
            'UGX' => 'UG',
            'GHS' => 'GH',
            'ZMW' => 'ZM',
            'RWF' => 'RW',
        );

        if (!is_null($currency)) {
            if (! isset($countriesMap[$currency])) {
                throw new \InvalidArgument("The currency $currency is not supported at checkout.");
            }
            return $countriesMap[$currency];
        }

        return $countriesMap[$baseCurrency];
    }
}
