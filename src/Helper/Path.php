<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Helper;

final class Path
{
	private function __construct()
	{
	}

	/**
	 * 0 => (string) namespace
	 * 1 => (string) name
	 * 2 => (?string) extension
	 *
	 * @param string $path
	 *
	 * @return array
	 */
	public static function parse(string $path): array
	{
		$namespace = explode('/', trim($path, " \t\n\r\0\x0B/"));
		$name = explode('.', array_pop($namespace));
		$startsWithDot = FALSE;

		if (1 < count($name) && '' === $name[0] ?? NULL) {
			unset($name[0]);
			$startsWithDot = TRUE;
		}

		$extension = 1 < count($name) ? array_pop($name) : NULL;

		return [
			implode('/', $namespace),
			($startsWithDot ? '.' : '') . implode('.', $name),
			$extension,
		];
	}
}
