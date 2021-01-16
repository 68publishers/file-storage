<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Nette\DI;

use Nette\DI\Definitions\Definition;

interface FileStorageDefinitionFactoryInterface
{
	/**
	 * @param string $name
	 * @param object $config
	 *
	 * @return bool
	 */
	public function canCreateFileStorage(string $name, object $config): bool;

	/**
	 * @param string $name
	 * @param object $config
	 *
	 * @return \Nette\DI\Definitions\Definition
	 */
	public function createFileStorage(string $name, object $config): Definition;
}
