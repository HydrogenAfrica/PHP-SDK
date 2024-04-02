<?php

declare(strict_types=1);

namespace HydrogenAfrica\Contract;

use HydrogenAfrica\Entities\Customer;

interface CustomerInterface
{
    public function create(array $data): Customer;
}
