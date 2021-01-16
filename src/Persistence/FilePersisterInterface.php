<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Persistence;

use League\Flysystem\FilesystemOperator;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use SixtyEightPublishers\FileStorage\Resource\ResourceInterface;

interface FilePersisterInterface
{
	public const OPTION_SUPPRESS_EXCEPTIONS = '68.suppress_exceptions';

	/**
	 * @return \League\Flysystem\FilesystemOperator
	 */
	public function getFilesystem(): FilesystemOperator;

	/**
	 * @param \SixtyEightPublishers\FileStorage\PathInfoInterface $pathInfo
	 *
	 * @return bool
	 */
	public function exists(PathInfoInterface $pathInfo): bool;

	/**
	 * Returns path of stored image
	 *
	 * @param \SixtyEightPublishers\FileStorage\Resource\ResourceInterface $resource
	 * @param array                                                        $config
	 *
	 * @return string
	 * @throws \SixtyEightPublishers\FileStorage\Exception\FilesystemException
	 */
	public function save(ResourceInterface $resource, array $config = []): string;

	/**
	 * @param \SixtyEightPublishers\FileStorage\PathInfoInterface $pathInfo
	 * @param array                                               $config
	 *
	 * @return void
	 * @throws \SixtyEightPublishers\FileStorage\Exception\FilesystemException
	 */
	public function delete(PathInfoInterface $pathInfo, array $config = []): void;
}
