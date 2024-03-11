<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Exception;

use BadMethodCallException;
use function sprintf;

final class IllegalMethodCallException extends BadMethodCallException implements ExceptionInterface
{
    public static function notAllowed(string $method): self
    {
        return new self(sprintf(
            'Calling the method %s() is not allowed.',
            $method,
        ));
    }
}
