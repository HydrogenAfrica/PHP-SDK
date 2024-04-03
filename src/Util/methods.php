<?php

declare(strict_types=1);

use HydrogenpayAfrica\Service\AccountPayment;
use HydrogenpayAfrica\Service\BankTransfer;
use HydrogenpayAfrica\Service\TokenizedCharge;
use HydrogenpayAfrica\Service\Transfer;
use HydrogenpayAfrica\Service\Ussd;

return [
    'account' => AccountPayment::class,
    'bank-transfer' => BankTransfer::class,
    'card' => CardPayment::class,
    'tokenize' => TokenizedCharge::class,
    'transfer' => Transfer::class,
];
