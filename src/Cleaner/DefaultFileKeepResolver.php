<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Cleaner;

use function in_array;

final class DefaultFileKeepResolver implements FileKeepResolverInterface
{
    /** @var array<string> */
    private array $files = [
        '.gitignore',
        '.gitkeep',
    ];

    public function isKept(string $filename): bool
    {
        return in_array($filename, $this->files, true);
    }
}
