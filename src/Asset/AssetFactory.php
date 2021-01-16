<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Asset;

use League\Flysystem\StorageAttributes;
use League\Flysystem\FilesystemOperator;
use SixtyEightPublishers\FileStorage\Exception\InvalidArgumentException;

final class AssetFactory implements AssetFactoryInterface
{
	/**
	 * {@inheritDoc}
	 *
	 * @throws \League\Flysystem\FilesystemException
	 */
	public function create(FilesystemOperator $localFilesystem, string $from, string $to): array
	{
		$to = trim($to, '\\/');

		if ($localFilesystem->fileExists($from)) {
			return [
				new Asset($from, $to),
			];
		}

		$normalizedDirectory = str_replace('\\', '/', rtrim($from, '\\/')) . '/';

		return $localFilesystem->listContents($normalizedDirectory, FilesystemOperator::LIST_DEEP)
			->filter(static function (StorageAttributes $attributes) {
				return $attributes->isFile();
			})
			->map(static function (StorageAttributes $attributes) use ($normalizedDirectory, $to) {
				if (0 !== strncmp($attributes->path(), $normalizedDirectory, strlen($normalizedDirectory))) {
					throw new InvalidArgumentException(sprintf(
						'Invalid asset paths. A path %s must starts with %s',
						$attributes->path(),
						$normalizedDirectory
					));
				}

				$namespace = empty($to) ? '' : $to . '/';

				return new Asset($attributes->path(), $namespace . substr($attributes->path(), strlen($normalizedDirectory)));
			})
			->toArray();
	}
}
