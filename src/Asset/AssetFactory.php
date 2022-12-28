<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Asset;

use League\Flysystem\FilesystemReader;
use League\Flysystem\StorageAttributes;
use League\Flysystem\FilesystemOperator;
use function trim;
use function rtrim;
use function strlen;
use function substr;
use function str_replace;

final class AssetFactory implements AssetFactoryInterface
{
	/**
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

		return $localFilesystem->listContents($normalizedDirectory, FilesystemReader::LIST_DEEP)
			->filter(static function (StorageAttributes $attributes) {
				return $attributes->isFile();
			})
			->map(static function (StorageAttributes $attributes) use ($normalizedDirectory, $to) {
				$namespace = empty($to) ? '' : $to . '/';

				return new Asset($attributes->path(), $namespace . substr($attributes->path(), strlen($normalizedDirectory)));
			})
			->toArray();
	}
}
