<?php

declare(strict_types=1);

namespace HydrogenpayAfrica\Contract;

interface Payment
{
    public function initiate(\HydrogenpayAfrica\Entities\Payload $payload): array;

    public function charge(\HydrogenpayAfrica\Entities\Payload $payload): array;

    public function save(callable $callback): void;

    public function verify(?string $transactionId): \stdClass;
}
