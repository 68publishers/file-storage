<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Resource;

use SixtyEightPublishers\FileStorage\PathInfoInterface;

interface ResourceInterface
{
    public function getPathInfo(): PathInfoInterface;

    public function getSource(): mixed;

    public function withPathInfo(PathInfoInterface $pathInfo): self;

    public function getMimeType(): ?string;

    public function getFilesize(): ?int;
}
