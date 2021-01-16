<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Console\Configurator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

interface CleanCommandConfiguratorInterface
{
	/**
	 * @param \Symfony\Component\Console\Command\Command $command
	 *
	 * @return void
	 */
	public function setupOptions(Command $command): void;

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 *
	 * @return array
	 */
	public function getCleanerOptions(InputInterface $input): array;
}
