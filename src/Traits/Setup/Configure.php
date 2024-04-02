<?php

declare(strict_types=1);

namespace HydrogenAfrica\Traits\Setup;

use HydrogenAfrica\Contract\ConfigInterface;
use HydrogenAfrica\Helper\Config;
use HydrogenAfrica\Config\ForkConfig;

trait Configure
{
    public static function bootstrap(?ConfigInterface $config = null): void
    {
        if (\is_null($config)) {
            include __DIR__ . '/../../../setup.php';

            if ('composer' === $hydrogen_installation) {
                $config = Config::setUp(
                    $keys[Config::TEST_AUTH_TOKEN],
                    $keys[Config::LIVE_AUTH_TOKEN],
                    $keys[Config::MODE]
                );
            }

            if ('manual' === $hydrogen_installation) {
                $config = ForkConfig::setUp(
                    $keys[ForkConfig::TEST_AUTH_TOKEN],
                    $keys[ForkConfig::LIVE_AUTH_TOKEN],
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
