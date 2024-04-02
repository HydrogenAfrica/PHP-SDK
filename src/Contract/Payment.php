<?php

declare(strict_types=1);

namespace HydrogenAfrica\Contract;

interface Payment
{
    public function initiate(\HydrogenAfrica\Entities\Payload $payload): array;

    public function charge(\HydrogenAfrica\Entities\Payload $payload): array;

    public function save(callable $callback): void;

    public function verify(?string $transactionId): \stdClass;
}
