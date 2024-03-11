<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Nette\DI;

final class Assets
{
    /**
     * @param array<string> $paths
     */
    public function __construct(
        public readonly string $storageName,
        public readonly array $paths,
    ) {}
}
