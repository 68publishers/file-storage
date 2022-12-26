<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Latte;

use SixtyEightPublishers\FileStorage\FileInfoInterface;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;

final class FileStorageFunctionSet
{
	private function __construct()
	{
	}

	/**
	 * @return array{file_info: callable}
	 */
	public static function functions(FileStorageProviderInterface $fileStorageProvider): array
	{
		return [
			'file_info' => fn (PathInfoInterface|string $pathInfo, ?string $storageName = null) => self::createFileInfo($fileStorageProvider, $pathInfo, $storageName),
		];
	}

	private static function createFileInfo(FileStorageProviderInterface $fileStorageProvider, PathInfoInterface|string $pathInfo, ?string $storageName = null): FileInfoInterface
	{
		if ($pathInfo instanceof FileInfoInterface) {
			return $pathInfo;
		}

		$storage = $fileStorageProvider->get($storageName);

		if (!$pathInfo instanceof PathInfoInterface) {
			$pathInfo = $storage->createPathInfo((string) $pathInfo);
		}

		return $storage->createFileInfo($pathInfo);
	}
}
