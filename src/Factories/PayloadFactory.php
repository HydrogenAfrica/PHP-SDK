<?php

declare(strict_types=1);

namespace HydrogenAfrica\Factories;

use HydrogenAfrica\Contract\FactoryInterface;
use HydrogenAfrica\Entities\Payload as Load;

/**
 * Class PayloadFactory.
 */
class PayloadFactory implements FactoryInterface
{
    protected array $requiredParams = [
        'amount', 'currency', 'customerName',
    ];

    public function create(array $data): Load
    {
        $check = $this->validSuppliedData($data);
        if (!$check['result']) {
            throw new \InvalidArgumentException(
                "<b><span style='color:red'>" . $check['missing_param'] . '</span></b>' .
                    ' is required in the payload'
            );
        }

        // $txRef = $data['tx_ref'];
        $customerName = $data['customerName'];
        $customerEmail = $data['email'];
        $amount = $data['amount'];
        $paymentDescription = $data['description'];
        $otherPaymentInformation = $data['meta'] ?? null;
        $callbackUrl = $data['callback'];

        $payload = new Load();

        $payload->set('currency', 'NGN');
        $payload->set('amount', $amount);
        $payload->set('email', $customerEmail);
        $payload->set('description', $paymentDescription);
        $payload->set('customerName', $customerName);
        $payload->set('meta', $otherPaymentInformation);
        $payload->set('callback', $callbackUrl);

        return $payload;
    }


    public function validSuppliedData(array $data): array
    {
        $params = $this->requiredParams;

        foreach ($params as $param) {
            if (!array_key_exists($param, $data)) {
                return ['missing_param' => $param, 'result' => false];
            }
        }

        return ['missing_param' => null, 'result' => true];
    }
}
