<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage;

use IteratorAggregate;

interface FileStorageProviderInterface extends IteratorAggregate
{
	/**
	 * NULL = default
	 *
	 * @param string|NULL $name
	 *
	 * @return \SixtyEightPublishers\FileStorage\FileStorageInterface
	 */
	public function get(?string $name = NULL): FileStorageInterface;
}
