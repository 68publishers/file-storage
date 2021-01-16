<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Asset;

use League\Flysystem\FilesystemOperator;

interface AssetFactoryInterface
{
	/**
	 * @param \League\Flysystem\FilesystemOperator $localFilesystem
	 * @param string                               $from
	 * @param string                               $to
	 *
	 * @return \SixtyEightPublishers\FileStorage\Asset\AssetInterface[]
	 */
	public function create(FilesystemOperator $localFilesystem, string $from, string $to): array;
}
