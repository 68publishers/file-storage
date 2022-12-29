<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Fixtures;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Configurator\CleanCommandConfiguratorInterface;

final class CustomCleanCommandConfigurator implements CleanCommandConfiguratorInterface
{
	public function setupOptions(Command $command): void
	{
	}

	public function getCleanerOptions(InputInterface $input): array
	{
		return [];
	}
}
