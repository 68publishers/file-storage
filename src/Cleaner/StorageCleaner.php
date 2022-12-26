<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Cleaner;

use League\Flysystem\FilesystemReader;
use League\Flysystem\StorageAttributes;
use League\Flysystem\FilesystemOperator;
use function count;
use function assert;
use function explode;
use function array_pop;

final class StorageCleaner implements StorageCleanerInterface
{
	public function __construct(
		private readonly FileKeepResolverInterface $fileKeepResolver
	) {
	}

	/**
	 * @throws \League\Flysystem\FilesystemException
	 */
	public function getCount(FilesystemOperator $filesystemOperator, array $options = []): int
	{
		$contents = $filesystemOperator->listContents(($options[self::OPTION_FILESYSTEM_PREFIX ] ?? '') . ($options[self::OPTION_NAMESPACE] ?? ''), FilesystemReader::LIST_DEEP)
			->filter(function (StorageAttributes $attributes) {
				if (!$attributes->isFile()) {
					return false;
				}

				$parts = explode(DIRECTORY_SEPARATOR, $attributes->path());

				return !$this->fileKeepResolver->isKept(array_pop($parts));
			})
			->toArray();

		return count($contents);
	}

	/**
	 * @throws \League\Flysystem\FilesystemException
	 */
	public function clean(FilesystemOperator $filesystemOperator, array $options = []): void
	{
		$prefix = $options[self::OPTION_FILESYSTEM_PREFIX ] ?? '';
		$contents = $filesystemOperator->listContents($prefix . ($options[self::OPTION_NAMESPACE] ?? ''), FilesystemReader::LIST_SHALLOW)
			->filter(function (StorageAttributes $attributes) {
				if (!$attributes->isFile()) {
					return true;
				}

				$parts = explode(DIRECTORY_SEPARATOR, $attributes->path());

				return !$this->fileKeepResolver->isKept(array_pop($parts));
			});

		foreach ($contents as $attributes) {
			assert($attributes instanceof StorageAttributes);

			if ($attributes->isDir()) {
				$filesystemOperator->deleteDirectory($prefix . $attributes->path());

				continue;
			}

			if ($attributes->isFile()) {
				$filesystemOperator->delete($prefix . $attributes->path());
			}
		}
	}
}
