<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Persistence;

use League\Flysystem\FilesystemOperator;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use SixtyEightPublishers\FileStorage\Resource\ResourceInterface;
use SixtyEightPublishers\FileStorage\Exception\FilesystemException;
use League\Flysystem\FilesystemException as LeagueFilesystemException;

class FilePersister implements FilePersisterInterface
{
	/** @var \League\Flysystem\FilesystemOperator  */
	private $filesystemOperator;

	/**
	 * @param \League\Flysystem\FilesystemOperator $filesystemOperator
	 */
	public function __construct(FilesystemOperator $filesystemOperator)
	{
		$this->filesystemOperator = $filesystemOperator;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFilesystem(): FilesystemOperator
	{
		return $this->filesystemOperator;
	}

	/**
	 * {@inheritdoc}
	 */
	public function exists(PathInfoInterface $pathInfo): bool
	{
		try {
			return $this->filesystemOperator->fileExists($pathInfo->getPath());
		} catch (LeagueFilesystemException $e) {
			return FALSE;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function save(ResourceInterface $resource, array $config = []): string
	{
		try {
			$path = $resource->getPathInfo()->getPath();
			$source = $resource->getSource();

			if (is_resource($source)) {
				$this->filesystemOperator->writeStream($path, $resource->getSource(), $config);
			} else {
				$this->filesystemOperator->write($path, $source, $config);
			}

			return $path;
		} catch (LeagueFilesystemException $e) {
			if (TRUE === ($config[self::OPTION_SUPPRESS_EXCEPTIONS] ?? FALSE)) {
				return $path ?? '';
			}

			throw new FilesystemException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete(PathInfoInterface $pathInfo, array $config = []): void
	{
		try {
			$this->filesystemOperator->delete($pathInfo->getPath());
		} catch (LeagueFilesystemException $e) {
			if (TRUE === ($config[self::OPTION_SUPPRESS_EXCEPTIONS] ?? FALSE)) {
				return;
			}

			throw new FilesystemException($e->getMessage(), $e->getCode(), $e);
		}
	}
}
