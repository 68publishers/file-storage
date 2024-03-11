<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Asset;

interface PathsProviderInterface
{
    /**
     * @return array<string, string>
     */
    public function getPaths(string $name): array;
}
