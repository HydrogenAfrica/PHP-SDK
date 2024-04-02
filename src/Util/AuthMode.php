<?php

declare(strict_types=1);

namespace HydrogenAfrica\Util;

class AuthMode
{
    public const PIN = 'pin';
    public const REDIRECT = 'redirect';
    public const VALIDATE = 'validate';
    public const CALLBACK = 'callback';
    public const OTP = 'otp';
    public const USSD = 'ussd';
    public const BANKTRANSFER = 'banktransfer';
}
