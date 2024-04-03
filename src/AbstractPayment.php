<?php

declare(strict_types=1);

namespace HydrogenpayAfrica;

use HydrogenpayAfrica\Contract\ConfigInterface;
use HydrogenpayAfrica\EventHandlers\EventHandlerInterface;
use HydrogenpayAfrica\Helper\EnvVariables;
use HydrogenpayAfrica\Traits\ApiOperations as Api;
use HydrogenpayAfrica\Traits\PayloadOperations as Payload;
use Psr\Log\LoggerInterface;

abstract class AbstractPayment
{
    use Api\Post;
    use Api\Get;
    use Payload\Prepare;

    public string $secretKey;

    public string $publicKey;
    public string $transactionRef;
    public $type;
    public LoggerInterface $logger;
    //    protected ?string $integrityHash = null;
    protected string $payButtonText = 'Proceed with Payment';
    protected string $redirectUrl;
    protected array $meta = [];
    //protected $env;
    protected string $transactionPrefix;
    // public $logger;
    protected EventHandlerInterface $handler;
    protected string $baseUrl;
    protected string $transactionData;
    protected bool $overrideTransactionReference;
    protected int $requeryCount = 0;
    protected static ?array $methods = null;

    //Payment information
    protected $account;
    protected $key;
    protected $pin;
    protected $options;
    protected string $amount;
    protected $paymentOptions = null;
    protected $customDescription;
    protected $customLogo;
    protected $customTitle;
    protected $country;
    protected $currency;
    protected $customerEmail;
    protected $customerFirstname;
    protected $customerLastname;
    protected $customerPhone;

    //EndPoints
    protected string $end_point;
    protected string $flwRef;
    protected static ?ConfigInterface $config = null;

    public function __construct()
    {
        $this->transactionPrefix = self::$config::DEFAULT_PREFIX . '_';
        $this->baseUrl = EnvVariables::BASE_URL;
    }

    public function getConfig()
    {
        return self::$config;
    }

    abstract public function initialize(): void;
}
