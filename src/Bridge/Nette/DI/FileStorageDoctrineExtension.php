<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Nette\DI;

use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\DI\CompilerExtension;
use SixtyEightPublishers\FileStorage\Exception\RuntimeException;
use SixtyEightPublishers\DoctrineBridge\Bridge\Nette\DI\DatabaseType;
use SixtyEightPublishers\DoctrineBridge\Bridge\Nette\DI\DoctrineBridgeExtension;
use SixtyEightPublishers\FileStorage\Bridge\Nette\DbalType\FileInfo\FileInfoType;
use SixtyEightPublishers\DoctrineBridge\Bridge\Nette\DI\DatabaseTypeProviderInterface;
use SixtyEightPublishers\FileStorage\Bridge\Nette\DI\Config\FileStorageDoctrineConfig;
use function count;
use function assert;

final class FileStorageDoctrineExtension extends CompilerExtension implements DatabaseTypeProviderInterface
{
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'type_name' => Expect::string('file_info'),
		])->castTo(FileStorageDoctrineConfig::class);
	}

	public function loadConfiguration(): void
	{
		if (0 >= count($this->compiler->getExtensions(FileStorageExtension::class))) {
			throw new RuntimeException(sprintf(
				'The extension %s can be used only with %s.',
				self::class,
				FileStorageExtension::class
			));
		}

		if (0 >= count($this->compiler->getExtensions(DoctrineBridgeExtension::class))) {
			throw new RuntimeException(sprintf(
				'The extension %s can be used only with %s.',
				self::class,
				DoctrineBridgeExtension::class
			));
		}
	}

	public function getDatabaseTypes(): array
	{
		$config = $this->getConfig();
		assert($config instanceof FileStorageDoctrineConfig);

		return [
			new DatabaseType($config->type_name, FileInfoType::class),
		];
	}
}
