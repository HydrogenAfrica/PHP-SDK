<?php

declare(strict_types=1);

use HydrogenAfrica\Service\AccountPayment;
use HydrogenAfrica\Service\BankTransfer;
use HydrogenAfrica\Service\TokenizedCharge;
use HydrogenAfrica\Service\Transfer;
use HydrogenAfrica\Service\Ussd;

return [
    'account' => AccountPayment::class,
    'bank-transfer' => BankTransfer::class,
    'card' => CardPayment::class,
    'tokenize' => TokenizedCharge::class,
    'transfer' => Transfer::class,
];
