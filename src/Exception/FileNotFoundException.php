<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Exception;

use Throwable;
use function sprintf;

final class FileNotFoundException extends FilesystemException
{
    public function __construct(string $path, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf(
            'File "%s" not found.',
            $path,
        ), $code, $previous);
    }
}
