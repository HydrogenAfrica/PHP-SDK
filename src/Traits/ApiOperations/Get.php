<?php

declare(strict_types=1);

namespace HydrogenpayAfrica\Traits\ApiOperations;

use HydrogenpayAfrica\Contract\ConfigInterface;
use HydrogenpayAfrica\Exception\ApiException;
use HydrogenpayAfrica\Service\Service as Http;
use Psr\Http\Client\ClientExceptionInterface;
use stdClass;

trait Get
{
    /**
     * @param  ConfigInterface $config
     * @param  string          $url
     * @param  string          $data
     * @return stdClass
     * @throws ClientExceptionInterface
     * @throws ApiException
     */
    public function getURL(ConfigInterface $config, string $url): stdClass
    {

        $response = (new Http($config))->request(null, 'GET', $url);

        if ($response->status === 'success') {
            return $response;
        }
        throw new ApiException($response->message);
    }
}
