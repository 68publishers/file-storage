<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Cleaner;

interface FileKeepResolverInterface
{
    public function isKept(string $filename): bool;
}
