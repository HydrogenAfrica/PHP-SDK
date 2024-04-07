<?php

declare(strict_types=1);

namespace HydrogenpayAfrica\Traits\PayloadOperations;

use HydrogenpayAfrica\AbstractPayment;

trait Prepare
{
    /**
     * Generates a transaction reference number for the transactions
     *
     * @return Prepare|AbstractPayment
     */
    public function createReferenceNumber(): self
    {
        $this->logger->notice('Generating Reference Number....');
        $this->txref = uniqid($this->transactionPrefix);
        $this->logger->notice('Generated Reference Number....' . $this->txref);
        return $this;
    }

}
