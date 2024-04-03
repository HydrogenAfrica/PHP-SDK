<?php

declare(strict_types=1);

namespace HydrogenpayAfrica\Service;

use HydrogenpayAfrica\Factories\PayloadFactory as Factory;

/**
 * Class Payload.
 *
 * @deprecated use \HydrogenpayAfrica\Factories\PayloadFactory instead
 */
class Payload
{
    private Factory $payloadFactory;
    public function __construct()
    {
        $this->payloadFactory = new Factory();
    }

    public function create(array $data): \HydrogenpayAfrica\Entities\Payload
    {
        return $this->payloadFactory->create($data);
    }

    public function validSuppliedData(array $data): array
    {
        return $this->payloadFactory->validSuppliedData($data);
    }
}
