<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage;

use IteratorAggregate;

/**
 * @extends IteratorAggregate<string, FileStorageInterface>
 */
interface FileStorageProviderInterface extends IteratorAggregate
{
	/**
	 * @param string|null $name Null is default
	 */
	public function get(?string $name = null): FileStorageInterface;
}
