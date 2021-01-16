<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Console\Configurator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use SixtyEightPublishers\FileStorage\Cleaner\StorageCleanerInterface;

final class BaseCleanCommandConfigurator implements CleanCommandConfiguratorInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function setupOptions(Command $command): void
	{
		$command->addOption(StorageCleanerInterface::OPTION_NAMESPACE, NULL, InputOption::VALUE_OPTIONAL, 'Search only in a specific namespace', NULL);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getCleanerOptions(InputInterface $input): array
	{
		if ($input->hasOption(StorageCleanerInterface::OPTION_NAMESPACE)) {
			return [
				StorageCleanerInterface::OPTION_NAMESPACE => $input->getOption(StorageCleanerInterface::OPTION_NAMESPACE),
			];
		}

		return [];
	}
}
