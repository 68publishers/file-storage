<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Exception;

use Throwable;

final class FileNotFoundException extends FilesystemException
{
	/**
	 * @param string          $path
	 * @param int             $code
	 * @param \Throwable|NULL $previous
	 */
	public function __construct(string $path, int $code = 0, Throwable $previous = NULL)
	{
		parent::__construct(sprintf(
			'File "%s" not found.',
			$path
		), $code, $previous);
	}
}
