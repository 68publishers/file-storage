<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Asset;

use function array_key_exists;
use function array_merge;

final class PathsProvider implements PathsProviderInterface
{
    /** @var array<string, array<string, string>>  */
    private array $pathsMap = [];

    /**
     * @param array<string, string> $paths
     */
    public function addPaths(string $name, array $paths): void
    {
        $this->pathsMap[$name] = array_merge($this->pathsMap[$name] ?? [], $paths);
    }

    public function getPaths(string $name): array
    {
        return array_key_exists($name, $this->pathsMap) ? $this->pathsMap[$name] : [];
    }
}
