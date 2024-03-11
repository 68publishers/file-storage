<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Configurator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use function array_merge;

final class CleanCommandConfiguratorRegistry implements CleanCommandConfiguratorInterface
{
    /** @var array<CleanCommandConfiguratorInterface> */
    private array $configurators;

    /**
     * @param array<CleanCommandConfiguratorInterface> $configurators
     */
    public function __construct(array $configurators)
    {
        $this->configurators = (static fn (CleanCommandConfiguratorInterface ...$configurators) => $configurators)(...$configurators);
    }

    public function setupOptions(Command $command): void
    {
        foreach ($this->configurators as $configurator) {
            $configurator->setupOptions($command);
        }
    }

    public function getCleanerOptions(InputInterface $input): array
    {
        $options = [];

        foreach ($this->configurators as $configurator) {
            $options[] = $configurator->getCleanerOptions($input);
        }

        return array_merge([], ...$options);
    }
}
