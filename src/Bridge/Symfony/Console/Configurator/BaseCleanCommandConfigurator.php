<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Configurator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use SixtyEightPublishers\FileStorage\Cleaner\StorageCleanerInterface;
use function is_string;

final class BaseCleanCommandConfigurator implements CleanCommandConfiguratorInterface
{
	public function setupOptions(Command $command): void
	{
		$command->addOption(StorageCleanerInterface::OPTION_NAMESPACE, null, InputOption::VALUE_REQUIRED, 'Search only in a specific namespace');
	}

	public function getCleanerOptions(InputInterface $input): array
	{
		$namespace = $input->getOption(StorageCleanerInterface::OPTION_NAMESPACE);

		if (is_string($namespace)) {
			return [
				StorageCleanerInterface::OPTION_NAMESPACE => $namespace,
			];
		}

		return [];
	}
}
