<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Resource;

use SixtyEightPublishers\FileStorage\PathInfoInterface;

class SimpleResource implements ResourceInterface
{
    /**
     * @param string|resource $source
     */
    public function __construct(
        private PathInfoInterface $pathInfo,
        private readonly mixed $source,
    ) {}

    public function getPathInfo(): PathInfoInterface
    {
        return $this->pathInfo;
    }

    public function getSource(): mixed
    {
        return $this->source;
    }

    public function withPathInfo(PathInfoInterface $pathInfo): static
    {
        $resource = clone $this;
        $resource->pathInfo = $pathInfo;

        return $resource;
    }
}
