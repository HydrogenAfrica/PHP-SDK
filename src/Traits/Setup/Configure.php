<?php

declare(strict_types=1);

namespace HydrogenpayAfrica\Traits\Setup;

use HydrogenpayAfrica\Contract\ConfigInterface;
use HydrogenpayAfrica\Helper\Config;
use HydrogenpayAfrica\Config\ForkConfig;

trait Configure
{
    public static function bootstrap(?ConfigInterface $config = null): void
    {
        if (\is_null($config)) {
            include __DIR__ . '/../../../setup.php';

            if ('composer' === $hydrogenpay_installation) {
                $config = Config::setUp(
                    $keys[Config::SANDBOX],
                    $keys[Config::LIVE_API_KEY],
                    $keys[Config::MODE]
                );
            }

            if ('manual' === $hydrogenpay_installation) {
                $config = ForkConfig::setUp(
                    $keys[ForkConfig::SANDBOX],
                    $keys[ForkConfig::LIVE_API_KEY],
                    $keys[ForkConfig::MODE]
                );
            }
        }

        if (\is_null(self::$config)) {
            self::$config = $config;
        }

        self::$methods = include __DIR__ . '/../../Util/methods.php';
    }
}
