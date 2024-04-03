<?php

declare(strict_types=1);

namespace HydrogenpayAfrica\Traits\ApiOperations;

use HydrogenpayAfrica\Contract\ConfigInterface;
use HydrogenpayAfrica\Service\Service as Http;
use Psr\Http\Client\ClientExceptionInterface;

trait Delete
{
    public function deleteURL(ConfigInterface $config, string $url): string
    {
        $response = (new Http($config))->request(null, 'DELETE', $url);

        return '';
    }
}
