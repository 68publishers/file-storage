<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Cleaner;

use League\Flysystem\StorageAttributes;
use League\Flysystem\FilesystemOperator;

final class StorageCleaner implements StorageCleanerInterface
{
	/** @var \SixtyEightPublishers\FileStorage\Cleaner\FileKeepResolverInterface  */
	private $fileKeepResolver;

	/**
	 * @param \SixtyEightPublishers\FileStorage\Cleaner\FileKeepResolverInterface $fileKeepResolver
	 */
	public function __construct(FileKeepResolverInterface $fileKeepResolver)
	{
		$this->fileKeepResolver = $fileKeepResolver;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws \League\Flysystem\FilesystemException
	 */
	public function getCount(FilesystemOperator $filesystemOperator, array $options = []): int
	{
		$contents = $filesystemOperator->listContents(($options[self::OPTION_FILESYSTEM_PREFIX ] ?? '') . ($options[self::OPTION_NAMESPACE] ?? ''), FilesystemOperator::LIST_DEEP)
			->filter(function (StorageAttributes $attributes) {
				if (!$attributes->isFile()) {
					return FALSE;
				}

				$parts = explode(DIRECTORY_SEPARATOR, $attributes->path());

				return !$this->fileKeepResolver->isKept(array_pop($parts));
			})
			->toArray();

		return count($contents);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws \League\Flysystem\FilesystemException
	 */
	public function clean(FilesystemOperator $filesystemOperator, array $options = []): void
	{
		$prefix = $options[self::OPTION_FILESYSTEM_PREFIX ] ?? '';
		$contents = $filesystemOperator->listContents($prefix . ($options[self::OPTION_NAMESPACE] ?? ''), FilesystemOperator::LIST_SHALLOW)
			->filter(function (StorageAttributes $attributes) {
				if (!$attributes->isFile()) {
					return TRUE;
				}

				$parts = explode(DIRECTORY_SEPARATOR, $attributes->path());

				return !$this->fileKeepResolver->isKept(array_pop($parts));
			});

		/** @var \League\Flysystem\StorageAttributes $attributes */
		foreach ($contents as $attributes) {
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
