<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Resource;

use League\Flysystem\FilesystemReader;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use SixtyEightPublishers\FileStorage\Exception\FilesystemException;
use SixtyEightPublishers\FileStorage\Exception\FileNotFoundException;
use League\Flysystem\FilesystemException as LeagueFilesystemException;

final class ResourceFactory implements ResourceFactoryInterface
{
	/** @var \League\Flysystem\FilesystemReader  */
	private $filesystemReader;

	/**
	 * @param \League\Flysystem\FilesystemReader $filesystemReader
	 */
	public function __construct(FilesystemReader $filesystemReader)
	{
		$this->filesystemReader = $filesystemReader;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws \League\Flysystem\FilesystemException
	 */
	public function createResource(PathInfoInterface $pathInfo): ResourceInterface
	{
		$path = $pathInfo->getPath();

		if (FALSE === $this->filesystemReader->fileExists($path)) {
			throw new FileNotFoundException($path);
		}

		try {
			$source = $this->filesystemReader->readStream($path);
		} catch (LeagueFilesystemException $e) {
			throw new FilesystemException(sprintf(
				'Can not read stream from file %s',
				$path
			), 0, $e);
		}

		if (FALSE === $source) {
			throw new FilesystemException(sprintf(
				'Can not read stream from file %s',
				$path
			), 0);
		}

		return new Resource($pathInfo, $source);
	}

	/**
	 * {@inheritdoc}
	 */
	public function createResourceFromLocalFile(PathInfoInterface $pathInfo, string $filename): ResourceInterface
	{
		error_clear_last();

		if (!file_exists($filename)) {
			throw new FileNotFoundException($filename);
		}

		$resource = @fopen($filename, 'rb');

		if (FALSE === $resource) {
			throw new FilesystemException(sprintf(
				'Can not read stream from file %s. %s',
				$filename,
				error_get_last()['message'] ?? ''
			), 0);
		}

		return new Resource($pathInfo, $resource);
	}
}
