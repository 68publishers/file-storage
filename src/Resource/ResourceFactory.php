<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Resource;

use League\Flysystem\FilesystemReader;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use SixtyEightPublishers\FileStorage\Exception\FilesystemException;
use SixtyEightPublishers\FileStorage\Exception\FileNotFoundException;
use League\Flysystem\FilesystemException as LeagueFilesystemException;
use function sprintf;
use function file_exists;
use function error_get_last;
use function error_clear_last;

final class ResourceFactory implements ResourceFactoryInterface
{
	public function __construct(
		private readonly FilesystemReader $filesystemReader
	) {
	}

	/**
	 * @throws \SixtyEightPublishers\FileStorage\Exception\FileNotFoundException
	 * @throws \League\Flysystem\FilesystemException
	 * @throws \SixtyEightPublishers\FileStorage\Exception\FilesystemException
	 */
	public function createResource(PathInfoInterface $pathInfo): ResourceInterface
	{
		$path = $pathInfo->getPath();

		if (false === $this->filesystemReader->fileExists($path)) {
			throw new FileNotFoundException($path);
		}

		try {
			$source = $this->filesystemReader->readStream($path);
		} catch (LeagueFilesystemException $e) {
			throw new FilesystemException(sprintf(
				'Can not read stream from file "%s".',
				$path
			), 0, $e);
		}

		return new SimpleResource($pathInfo, $source);
	}

	public function createResourceFromLocalFile(PathInfoInterface $pathInfo, string $filename): ResourceInterface
	{
		error_clear_last();

		if (!file_exists($filename)) {
			throw new FileNotFoundException($filename);
		}

		$resource = @fopen($filename, 'rb');

		if (false === $resource) {
			throw new FilesystemException(sprintf(
				'Can not read stream from file "%s". %s',
				$filename,
				error_get_last()['message'] ?? ''
			), 0);
		}

		return new SimpleResource($pathInfo, $resource);
	}
}
