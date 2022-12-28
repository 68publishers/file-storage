<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Nette\DI;

use Nette\DI\Definitions\ServiceDefinition;
use SixtyEightPublishers\FileStorage\Bridge\Nette\DI\Config\StorageConfig;

interface FileStorageDefinitionFactoryInterface
{
	public function canCreateFileStorage(string $name, StorageConfig $config): bool;

	public function createFileStorage(string $name, StorageConfig $config): ServiceDefinition;
}
