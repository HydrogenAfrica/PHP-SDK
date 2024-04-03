<?php

declare(strict_types=1);

namespace HydrogenAfrica\Traits\ApiOperations;

use HydrogenAfrica\Contract\ConfigInterface;
use HydrogenAfrica\Service\Service as Http;
use Psr\Http\Client\ClientExceptionInterface;


trait Post
{
    /**
     * @param ConfigInterface $config
     * @param array           $data
     *
     * @return string
     * @throws ClientExceptionInterface
     */
    public function postURL(ConfigInterface $config, array $data): string
    {

        $mode = self::$config->getMode();

        if ($mode == 'test') {

            $response = (new Http(self::$config))->request($data, 'POSTTEST');
        } else {

            $response = (new Http(self::$config))->request($data, 'POSTLIVE');
        }

        return $response->data->status;
    }
}
