<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Resource;

use SixtyEightPublishers\FileStorage\PathInfoInterface;

interface ResourceInterface
{
    public function getPathInfo(): PathInfoInterface;

    /**
     * @return string|resource
     */
    public function getSource(): mixed;

    public function withPathInfo(PathInfoInterface $pathInfo): static;
}
