<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Persistence;

use League\Flysystem\FilesystemOperator;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use SixtyEightPublishers\FileStorage\Resource\ResourceInterface;
use SixtyEightPublishers\FileStorage\Exception\FilesystemException;
use League\Flysystem\FilesystemException as LeagueFilesystemException;
use function is_resource;

class FilePersister implements FilePersisterInterface
{
	public function __construct(
		private readonly FilesystemOperator $filesystemOperator,
	) {
	}

	public function getFilesystem(): FilesystemOperator
	{
		return $this->filesystemOperator;
	}

	public function exists(PathInfoInterface $pathInfo): bool
	{
		try {
			return $this->filesystemOperator->fileExists($pathInfo->getPath());
		} catch (LeagueFilesystemException $e) {
			return false;
		}
	}

	public function save(ResourceInterface $resource, array $config = []): string
	{
		$path = $resource->getPathInfo()->getPath();

		try {
			$source = $resource->getSource();

			if (is_resource($source)) {
				$this->filesystemOperator->writeStream($path, $resource->getSource(), $config);
			} else {
				$this->filesystemOperator->write($path, $source, $config);
			}

			return $path;
		} catch (LeagueFilesystemException $e) {
			if (true === ($config[self::OPTION_SUPPRESS_EXCEPTIONS] ?? false)) {
				return $path;
			}

			throw new FilesystemException($e->getMessage(), $e->getCode(), $e);
		}
	}

	public function delete(PathInfoInterface $pathInfo, array $config = []): void
	{
		try {
			$this->filesystemOperator->delete($pathInfo->getPath());
		} catch (LeagueFilesystemException $e) {
			if (true === ($config[self::OPTION_SUPPRESS_EXCEPTIONS] ?? false)) {
				return;
			}

			throw new FilesystemException($e->getMessage(), $e->getCode(), $e);
		}
	}
}
