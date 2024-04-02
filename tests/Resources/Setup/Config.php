<?php

declare(strict_types=1);

namespace HydrogenAfrica\Test\Resources\Setup;

use HydrogenAfrica\Contract\ConfigInterface;
use HydrogenAfrica\Helper\EnvVariables;
use GuzzleHttp\Client;

use function is_null;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Psr\Http\Client\ClientInterface;

class Config implements ConfigInterface
{
    public const LIVE_AUTH_TOKEN = 'LIVE_AUTH_TOKEN';
    public const TEST_AUTH_TOKEN = 'TEST_AUTH_TOKEN';
    public const ENCRYPTION_KEY = 'ENCRYPTION_KEY';
    public const ENV = 'ENV';
    public const DEFAULT_PREFIX = 'FW|PHP';
    public const LOG_FILE_NAME = 'hydrogen-php.log';
    protected Logger $logger;
    private string $secret;
    private string $public;

    private static ?Config $instance = null;
    private string $env;
    private ClientInterface $http;
    private string $enc;

    private function __construct(
        string $secretKey,
        string $publicKey,
        string $encryptKey,
        string $mode
    )
    {
        $this->secret = $secretKey;
        $this->public = $publicKey;
        $this->enc = $encryptKey;
        $this->env = $mode;
        # when creating a custom config, you may choose to use other dependencies here.
        # http-client - Guzzle, logger - Monolog.
        $this->http = new Client(['base_uri' => EnvVariables::BASE_URL, 'timeout' => 60 ]);
        $log = new Logger('HydrogenAfrica/PHP'); // making use of Monolog;
        $this->logger = $log;
        $log->pushHandler(new RotatingFileHandler(self::LOG_FILE_NAME, 90));
    }

    public static function setUp(string $secretKey, string $publicKey, string $enc, string $env): ConfigInterface
    {
        if (is_null(self::$instance)) {
            return new Config($secretKey, $publicKey, $enc, $env);
        }
        return self::$instance;
    }

    public function getHttp(): ClientInterface
    {
        # for custom implementation, please ensure the
        return $this->http ?? new Client();
    }

    public function getLoggerInstance(): LoggerInterface
    {
        return $this->logger;
    }

    public function getEncryptkey(): string
    {
        return $this->enc;
    }

    public function getPublicKey(): string
    {
        return $this->public;
    }

    public function getSecretKey(): string
    {
        return $this->secret;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public static function getDefaultTransactionPrefix(): string
    {
        return self::DEFAULT_PREFIX;
    }
}
