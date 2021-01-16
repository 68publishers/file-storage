<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Nette\DI;

use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\DI\CompilerExtension;
use SixtyEightPublishers\DoctrineBridge\DI\DatabaseType;
use SixtyEightPublishers\FileStorage\Exception\RuntimeException;
use SixtyEightPublishers\DoctrineBridge\DI\DatabaseTypeProviderInterface;
use SixtyEightPublishers\FileStorage\Bridge\Nette\DbalType\FileInfo\FileInfoType;

final class FileStorageDoctrineExtension extends CompilerExtension implements DatabaseTypeProviderInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'type_name' => Expect::string('file_info'),
		]);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \SixtyEightPublishers\FileStorage\Exception\RuntimeException
	 */
	public function loadConfiguration(): void
	{
		if (0 >= count($this->compiler->getExtensions(FileStorageExtension::class))) {
			throw new RuntimeException(sprintf(
				'The extension %s can be used only with %s.',
				static::class,
				FileStorageExtension::class
			));
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDatabaseTypes(): array
	{
		return [
			new DatabaseType($this->config->type_name, FileInfoType::class),
		];
	}
}
