<?php

declare(strict_types=1);

namespace HydrogenAfrica\Config;

use HydrogenAfrica\EventHandlers\EventHandlerInterface;
use HydrogenAfrica\HydrogenAfrica;
use HydrogenAfrica\Contract\ConfigInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use HydrogenAfrica\Helper\EnvVariables;

abstract class AbstractConfig
{
    public const LIVE_AUTH_TOKEN = 'LIVE_AUTH_TOKEN';
    public const TEST_AUTH_TOKEN = 'TEST_AUTH_TOKEN';
    // public const ENCRYPTION_KEY = 'ENCRYPTION_KEY';
    public const MODE = 'MODE';
    public const DEFAULT_PREFIX = 'HY|PHP';
    public const LOG_FILE_NAME = 'hydrogen-php.log';
    protected Logger $logger;
    protected string $secret;
    protected string $public;

    protected static ?ConfigInterface $instance = null;
    protected string $mode;
    private ClientInterface $http;
    // protected string $enc;

    // protected function __construct(string $secret_key, string $live_auth_token, string $encrypt_key, string $env)
    protected function __construct(string $test_auth_token, string $live_auth_token, string $mode)

    {
        $this->secret = $test_auth_token;
        $this->public = $live_auth_token;
        // $this->enc = $encrypt_key;
        $this->mode = $mode;

        $this->http = new Client(
            [
            'base_uri' => EnvVariables::BASE_URL,
            'timeout' => 60,
            RequestOptions::VERIFY => \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath()
            ]
        );

        $log = new Logger('HydrogenAfrica/PHP');
        $this->logger = $log;
    }

    abstract public static function setUp(
        string $secretKey,
        string $publicKey,
        // string $enc,
        string $mode
    ): ConfigInterface;

    public function getHttp(): ClientInterface
    {
        return $this->http;
    }

    public function getLoggerInstance(): LoggerInterface
    {
        return $this->logger;
    }

    // abstract public function getEncryptkey(): string;

    abstract public function getPublicKey(): string;

    abstract public function getSecretKey(): string;

    abstract public function getMode(): string;

    public static function getDefaultTransactionPrefix(): string
    {
        return self::DEFAULT_PREFIX;
    }
}
