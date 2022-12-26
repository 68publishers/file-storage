<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Exception;

use InvalidArgumentException as OriginalInvalidArgumentException;

final class InvalidArgumentException extends OriginalInvalidArgumentException implements ExceptionInterface
{
}
