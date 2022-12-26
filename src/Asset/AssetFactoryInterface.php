<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Asset;

use League\Flysystem\FilesystemOperator;

interface AssetFactoryInterface
{
	/**
	 * @return array<AssetInterface>
	 */
	public function create(FilesystemOperator $localFilesystem, string $from, string $to): array;
}
