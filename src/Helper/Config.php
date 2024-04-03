<?php

declare(strict_types=1);

namespace HydrogenpayAfrica\Helper;

use HydrogenpayAfrica\Contract\ConfigInterface;
use GuzzleHttp\Client;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;


use function is_null;

/**
 * Class Payload.
 *
 * @deprecated use \HydrogenpayAfrica\Config\PackageConfig instead
 */
class Config implements ConfigInterface
{
    public const LIVE_AUTH_TOKEN = 'LIVE_AUTH_TOKEN';
    public const TEST_AUTH_TOKEN = 'TEST_AUTH_TOKEN';
    public const MODE = 'MODE';
    public const DEFAULT_PREFIX = 'HY|PHP';
    public const LOG_FILE_NAME = 'hydrogenpay-php.log';
    protected Logger $logger;
    private string $secret;
    private string $public;

    private static ?Config $instance = null;
    private string $mode;
    private ClientInterface $http;
    // private string $enc;

    private function __construct(string $secretKey, string $publicKey, string $mode)
    {
        $this->secret = $secretKey;
        $this->public = $publicKey;
        // $this->enc = $encryptKey;
        $this->mode = $mode;

        // when creating a custom config, you may choose to use other dependencies here.
        // http-client - Guzzle, logger - Monolog.
        $this->http = new Client(['base_uri' => EnvVariables::BASE_URL, 'timeout' => 60 ]);
        $log = new Logger('HydrogenpayAfrica/PHP');
        $this->logger = $log;
        $log->pushHandler(new RotatingFileHandler(__DIR__ . "../../../../../../" . self::LOG_FILE_NAME, 90));
    }

    public static function setUp(string $secretKey, string $publicKey, string $mode): ConfigInterface
    // public static function setUp(string $secretKey, string $publicKey, string $mode): ConfigInterface

    {
        if (is_null(self::$instance)) {
            // return new Config($secretKey, $publicKey, $enc, $env);
            return new Config($secretKey, $publicKey, $mode);

        }
        return self::$instance;
    }

    public function getHttp(): ClientInterface
    {
        return $this->http ?? new Client();
    }

    public function getLoggerInstance(): LoggerInterface
    {
        return $this->logger;
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
