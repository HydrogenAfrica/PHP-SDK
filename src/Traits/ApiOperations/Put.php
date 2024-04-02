<?php

declare(strict_types=1);

namespace HydrogenAfrica\Traits\ApiOperations;

use HydrogenAfrica\Contract\ConfigInterface;
use HydrogenAfrica\Service\Service as Http;
use Psr\Http\Client\ClientExceptionInterface;

trait Put
{
    /**
     * @param ConfigInterface $config
     * @param array           $data
     *
     * @return string
     * @throws ClientExceptionInterface
     */
    public function putURL(ConfigInterface $config, array $data): string
    {
        $response = (new Http($config))->request($data, 'PUT', $this->end_point);

        return '';
    }
}
