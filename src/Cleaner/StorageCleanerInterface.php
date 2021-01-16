<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Cleaner;

use League\Flysystem\FilesystemOperator;

interface StorageCleanerInterface
{
	public const OPTION_NAMESPACE = 'namespace';
	public const OPTION_FILESYSTEM_PREFIX = 'filesystem-prefix';

	/**
	 * @param \League\Flysystem\FilesystemOperator $filesystemOperator
	 * @param array                                $options
	 *
	 * @return int
	 */
	public function getCount(FilesystemOperator $filesystemOperator, array $options = []): int;

	/**
	 * @param \League\Flysystem\FilesystemOperator $filesystemOperator
	 * @param array                                $options
	 *
	 * @return void
	 */
	public function clean(FilesystemOperator $filesystemOperator, array $options = []): void;
}
