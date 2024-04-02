<?php

declare(strict_types=1);

namespace HydrogenAfrica\Service;

use HydrogenAfrica\Contract\ConfigInterface;
use HydrogenAfrica\Contract\FactoryInterface;
use HydrogenAfrica\Contract\ServiceInterface;
use HydrogenAfrica\Config\ForkConfig;
use HydrogenAfrica\Factories\CustomerFactory as Customer;
use HydrogenAfrica\Factories\PayloadFactory as Payload;
use HydrogenAfrica\Helper\Config;
use HydrogenAfrica\Helper\EnvVariables;
use Psr\Http\Client\ClientInterface;
use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Log\LoggerInterface;
use stdClass;

use function is_null;

class Service implements ServiceInterface
{
    public const ENDPOINT = '';
    public ?FactoryInterface $payload;
    public ?FactoryInterface $customer;
    protected string $baseUrl;
    protected string $mode;
    protected LoggerInterface $logger;
    protected ConfigInterface $config;
    protected string $url;
    protected string $testUrl;
    protected string $liveUrl;
    protected string $secret;
    private static string $name = 'service';
    private static ?ConfigInterface $spareConfig = null;
    private ClientInterface $http;

    public function __construct(?ConfigInterface $config = null)
    {
        self::bootstrap($config);
        $this->customer = new Customer();
        $this->payload = new Payload();
        $this->config = is_null($config) ? self::$spareConfig : $config;
        $this->http = $this->config->getHttp();
        $this->logger = $this->config->getLoggerInstance();
        // $this->secret = $this->config->getSecretKey();

        $this->secret = $this->config->getPublicKey();
        $this->url = EnvVariables::BASE_URL . '/';
        $this->baseUrl = EnvVariables::BASE_URL;

    }

    public function getName(): string
    {
        return self::$name;
    }

    /**
     * @param  array|null $data
     * @param  string     $verb
     * @param  string     $additionalurl
     * @return stdClass
     * @throws ClientExceptionInterface
     */
    public function request(
        ?array $data = null,
        string $verb = 'GET',
        string $additionalurl = '',
        bool $overrideUrl = false
    ): stdClass {

        // $secret = $this->config->getSecretKey();
        // $secret = $this->config->getPublicKey();
        $payload_hash = $data['payload_hash']; // Assuming 'payload_hash' is part of the $data array
        $hydrogen_url = $data['payload_url'];

        $url = $this->getUrl($overrideUrl, $additionalurl);

        switch ($verb) {
        case 'POST':
            $response = $this->http->request(
                // 'POST', $url, [
                'POST', $hydrogen_url, [
                'debug' => false, 
                'headers' => [
                    // 'Authorization' => "Bearer $secret",
                    'Authorization' => "Bearer $payload_hash",
                    'Content-Type' => 'application/json',
                ],
                'json' => $data,
                    ]
            );
            break;
        case 'PUT':
            $response = $this->http->request(
                'PUT', $url, [
                'debug' => false, 
                'headers' => [
                    'Authorization' => "Bearer $payload_hash",
                    'Content-Type' => 'application/json',
                ],
                'json' => $data ?? [],
                    ]
            );
            break;
        case 'DELETE':
            $response = $this->http->request(
                'DELETE', $url, [
                'debug' => false,
                'headers' => [
                    'Authorization' => "Bearer $payload_hash",
                    'Content-Type' => 'application/json',
                ],
                    ]
            );
            break;
        default:
            $response = $this->http->request(
                'GET', $url, [
                'debug' => false,
                'headers' => [
                    'Authorization' => "Bearer $payload_hash",
                    'Content-Type' => 'application/json',
                ],
                    ]
            );
            break;
        }

        $body = $response->getBody()->getContents();
        return json_decode($body);
    }

    protected function checkTransactionId($transactionId): void
    {
        $pattern = '/([0-9]){7}/';
        $is_valid = preg_match_all($pattern, $transactionId);

        if (! $is_valid) {
            $this->logger->warning('Transaction Service::cannot verify invalid transaction id. ');
            throw new InvalidArgumentException('cannot verify invalid transaction id.');
        }
    }

    private static function bootstrap(?ConfigInterface $config = null): void
    {
        if (is_null($config)) {
            include __DIR__ . '/../../setup.php';

            if ('composer' === $hydrogen_installation) {
                $config = Config::setUp(
                    $keys[Config::TEST_AUTH_TOKEN],
                    $keys[Config::LIVE_AUTH_TOKEN],
                    $keys[Config::MODE]
                );
            }

            if ('manual' === $hydrogen_installation) {
                $config = ForkConfig::setUp(
                    $keys[Config::TEST_AUTH_TOKEN],
                    $keys[Config::LIVE_AUTH_TOKEN],
                    $keys[Config::MODE]
                );
            }
        }
        self::$spareConfig = $config;
    }
    private function getUrl(bool $overrideUrl, string $additionalurl): string
    {
        if ($overrideUrl) {
            return $additionalurl;
        }

        return $this->url . $additionalurl;
    }
}
