<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Exception;

use BadMethodCallException;

final class IllegalMethodCallException extends BadMethodCallException implements ExceptionInterface
{
	/**
	 * @param string $method
	 *
	 * @return \SixtyEightPublishers\FileStorage\Exception\IllegalMethodCallException
	 */
	public static function notAllowed(string $method): self
	{
		return new self(sprintf(
			'Calling the method %s is not allowed.',
			$method
		));
	}
}
