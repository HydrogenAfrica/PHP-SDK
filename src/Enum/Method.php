<?php

declare(strict_types=1);

namespace HydrogenpayAfrica\Enum;

// use Cerbero\Enum\Concerns\Enumerates;

enum Method: string
{
    //    use Enumerates;
    case DEFAULT = 'default';
    case REDIRECT = 'redirect';
    case CARD = 'card';
    case USSD = 'ussd';
    case TRANSFER = 'transfer';
    
}
