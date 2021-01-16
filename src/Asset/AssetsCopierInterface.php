<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Asset;

use Psr\Log\LoggerInterface;
use SixtyEightPublishers\FileStorage\FileStorageInterface;

interface AssetsCopierInterface
{
	/**
	 * @param \SixtyEightPublishers\FileStorage\FileStorageInterface $fileStorage
	 * @param \Psr\Log\LoggerInterface|NULL                          $logger
	 *
	 * @return void
	 */
	public function copy(FileStorageInterface $fileStorage, ?LoggerInterface $logger = NULL): void;
}
