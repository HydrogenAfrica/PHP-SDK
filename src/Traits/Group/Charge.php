<?php

declare(strict_types=1);

namespace HydrogenpayAfrica\Traits\Group;

use HydrogenpayAfrica\Service\Transactions;
use Psr\Http\Client\ClientExceptionInterface;

trait Charge
{
    public function getEndpoint(): string
    {
        return 'charges';
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function verify(?string $transactionId = null): \stdClass
    {
        if (is_null($transactionId)) {
            $this->logger->error('Charge Group::To verify a transaction please pass a transactionId.');
            throw new \InvalidArgumentException('To verify a transaction please pass a transactionId.');
        }
        return (new Transactions($this->config))->verify($transactionId);
    }

    private function checkPayloadIsValid(\HydrogenpayAfrica\Entities\Payload $payload, string $criteria): bool
    {
        $this->logger->notice('Charge Group::Verifying Payload  ...');
        //if does not payload contains $criteria :: false
        if (! is_null($payload->get('otherData'))) {
            $additionalData = $payload->get('otherData');
            if (! isset($additionalData[$criteria])) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }
}
