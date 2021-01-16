<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Cleaner;

final class DefaultFileKeepResolver implements FileKeepResolverInterface
{
	/** @var array  */
	private $files = [
		'.gitignore',
		'.gitkeep',
	];

	/**
	 * {@inheritDoc}
	 */
	public function isKept(string $filename): bool
	{
		return in_array($filename, $this->files, TRUE);
	}
}
