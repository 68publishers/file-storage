<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Nette\DI\Config;

use Nette\DI\Definitions\Statement;

final class FilesystemConfig
{
    public Statement $adapter;

    /** @var array<string, mixed> */
    public array $config;
}
