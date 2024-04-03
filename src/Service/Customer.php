<?php

declare(strict_types=1);

namespace HydrogenpayAfrica\Service;

use HydrogenpayAfrica\Contract\CustomerInterface;
use HydrogenpayAfrica\Entities\Customer as Person;
use HydrogenpayAfrica\Factories\CustomerFactory;
use InvalidArgumentException;

/**
 * Class Customer.
 *
 * @deprecated use \HydrogenpayAfrica\Factories\CustomerFactory instead
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
