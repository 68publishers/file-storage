<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Cleaner;

interface FileKeepResolverInterface
{
	/**
	 * @param string $filename
	 *
	 * @return bool
	 */
	public function isKept(string $filename): bool;
}
