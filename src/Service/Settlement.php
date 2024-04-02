<?php

declare(strict_types=1);

namespace HydrogenAfrica\Service;

use HydrogenAfrica\Contract\ConfigInterface;
use HydrogenAfrica\EventHandlers\EventTracker;
use Psr\Http\Client\ClientExceptionInterface;

class Settlement extends Service
{
    use EventTracker;

    private string $name = 'settlements';
    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function get(string $id): \stdClass
    {
        $this->logger->notice("Settlement Service::Retrieving Settlement [{$id}].");
        self::startRecording();
        $response = $this->request(null, 'GET', $this->name . "/{$id}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function list(): \stdClass
    {
        $this->logger->notice('Settlement Service::Retrieving all Settlements.');
        self::startRecording();
        $response = $this->request(null, 'GET', $this->name);
        self::setResponseTime();
        return $response;
    }
}