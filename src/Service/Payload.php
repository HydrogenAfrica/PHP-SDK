<?php

declare(strict_types=1);

namespace HydrogenAfrica\Service;

use HydrogenAfrica\Factories\PayloadFactory as Factory;

/**
 * Class Payload.
 *
 * @deprecated use \HydrogenAfrica\Factories\PayloadFactory instead
 */
class Payload
{
    private Factory $payloadFactory;
    public function __construct()
    {
        $this->payloadFactory = new Factory();
    }

    public function create(array $data): \HydrogenAfrica\Entities\Payload
    {
        return $this->payloadFactory->create($data);
    }

    public function validSuppliedData(array $data): array
    {
        return $this->payloadFactory->validSuppliedData($data);
    }
}
