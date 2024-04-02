<?php

declare(strict_types=1);

namespace HydrogenAfrica\Service;

use HydrogenAfrica\Contract\CustomerInterface;
use HydrogenAfrica\Entities\Customer as Person;
use HydrogenAfrica\Factories\CustomerFactory;
use InvalidArgumentException;

/**
 * Class Customer.
 *
 * @deprecated use \HydrogenAfrica\Factories\CustomerFactory instead
 */
class Customer
{
    protected CustomerInterface $customerFactory;

    public function __construct()
    {
        $this->customerFactory = new CustomerFactory();
    }

    public function create(array $data = []): Person
    {
        return $this->customerFactory->create($data);
    }
}
