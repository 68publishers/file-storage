<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Asset;

use Psr\Log\LoggerInterface;
use SixtyEightPublishers\FileStorage\FileStorageInterface;

interface AssetsCopierInterface
{
	public function copy(FileStorageInterface $fileStorage, ?LoggerInterface $logger = null): void;
}
