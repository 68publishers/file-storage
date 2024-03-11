<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Configurator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

interface CleanCommandConfiguratorInterface
{
    public function setupOptions(Command $command): void;

    /**
     * @return array<string, mixed>
     */
    public function getCleanerOptions(InputInterface $input): array;
}
