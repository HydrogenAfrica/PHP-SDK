<?php

declare(strict_types=1);

namespace HydrogenpayAfrica\Contract;

use HydrogenpayAfrica\Helper\Config;

interface ServiceInterface
{
    public function __construct(Config $config);

    public function getName(): string;
}
