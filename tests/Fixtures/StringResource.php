<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Fixtures;

use SixtyEightPublishers\FileStorage\PathInfoInterface;
use SixtyEightPublishers\FileStorage\Resource\ResourceInterface;

final class StringResource implements ResourceInterface
{
    public function __construct(
        private readonly PathInfoInterface $pathInfo,
        private readonly string $source,
        private readonly ?string $mimeType = null,
        private readonly ?int $filesize = null,
    ) {}

    public function getPathInfo(): PathInfoInterface
    {
        return $this->pathInfo;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function withPathInfo(PathInfoInterface $pathInfo): ResourceInterface
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
        return $this->mimeType;
    }

    public function getFilesize(): ?int
    {
        return $this->mimeType;
    }
}
