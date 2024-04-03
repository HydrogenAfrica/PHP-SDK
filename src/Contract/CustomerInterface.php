<?php

declare(strict_types=1);

namespace HydrogenpayAfrica\Contract;

use HydrogenpayAfrica\Entities\Customer;

interface CustomerInterface
{
    public function create(array $data): Customer;
}
