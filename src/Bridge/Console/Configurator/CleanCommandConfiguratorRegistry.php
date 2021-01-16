<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Console\Configurator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

final class CleanCommandConfiguratorRegistry implements CleanCommandConfiguratorInterface
{
	/** @var \SixtyEightPublishers\FileStorage\Bridge\Console\Configurator\CleanCommandConfiguratorInterface[] */
	private $configurators;

	/**
	 * @param \SixtyEightPublishers\FileStorage\Bridge\Console\Configurator\CleanCommandConfiguratorInterface[] $configurators
	 */
	public function __construct(array $configurators)
	{
		$this->configurators = (static function (CleanCommandConfiguratorInterface ...$configurators) {
			return $configurators;
		})(...$configurators);
	}

	/**
	 * {@inheritDoc}
	 */
	public function setupOptions(Command $command): void
	{
		foreach ($this->configurators as $configurator) {
			$configurator->setupOptions($command);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getCleanerOptions(InputInterface $input): array
	{
		$options = [];

		foreach ($this->configurators as $configurator) {
			$options[] = $configurator->getCleanerOptions($input);
		}

		return array_merge([], ...$options);
	}
}
