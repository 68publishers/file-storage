<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Exception;

use RuntimeException as OriginalRuntimeException;

final class RuntimeException extends OriginalRuntimeException implements ExceptionInterface
{
}
