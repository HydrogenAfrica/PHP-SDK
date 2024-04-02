<?php

declare(strict_types=1);

namespace HydrogenAfrica\Contract;

use HydrogenAfrica\Helper\Config;

interface ServiceInterface
{
    public function __construct(Config $config);

    public function getName(): string;
}
