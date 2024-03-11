<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Helper;

use function array_pop;
use function count;
use function explode;
use function implode;
use function trim;

final class Path
{
    private function __construct() {}

    /**
     * 0 => (string) namespace
     * 1 => (string) name
     * 2 => (?string) extension
     *
     * @return array{0: string, 1: string, 2: ?string}
     */
    public static function parse(string $path): array
    {
        $namespace = explode('/', trim($path, " \t\n\r\0\x0B/"));
        $name = explode('.', array_pop($namespace));
        $startsWithDot = false;

        if (1 < count($name) && '' === ($name[0] ?? null)) {
            unset($name[0]);
            $startsWithDot = true;
        }

        $extension = 1 < count($name) ? array_pop($name) : null;

        return [
            implode('/', $namespace),
            ($startsWithDot ? '.' : '') . implode('.', $name),
            $extension,
        ];
    }
}
