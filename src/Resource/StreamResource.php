<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Resource;

use Closure;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use function is_int;
use function is_string;

final class StreamResource implements ResourceInterface
{
    /**
     * @param resource                                       $source
     * @param (Closure(self $resource): ?string)|string|null $mimeType
     * @param (Closure(self $resource): ?int)|int|null       $filesize
     */
    public function __construct(
        private readonly PathInfoInterface $pathInfo,
        private readonly mixed $source,
        private Closure|string|null $mimeType,
        private Closure|int|null $filesize,
    ) {}

    public function getPathInfo(): PathInfoInterface
    {
        return $this->pathInfo;
    }

    /**
     * @return resource
     */
    public function getSource(): mixed
    {
        return $this->source;
    }

    public function withPathInfo(PathInfoInterface $pathInfo): self
    {
        return new self(
            pathInfo: $pathInfo,
            source: $this->source,
            mimeType: $this->mimeType,
            filesize: $this->filesize,
        );
    }

    public function getMimeType(): ?string
    {
        if (null === $this->mimeType || is_string($this->mimeType)) {
            return $this->mimeType;
        }

        return $this->mimeType = ($this->mimeType)($this);
    }

    public function getFilesize(): ?int
    {
        if (null === $this->filesize || is_int($this->filesize)) {
            return $this->filesize;
        }

        return $this->filesize = ($this->filesize)($this);
    }
}
