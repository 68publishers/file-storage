<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage;

use SixtyEightPublishers\FileStorage\LinkGenerator\LinkGeneratorInterface;

class FileInfo implements FileInfoInterface
{
    public function __construct(
        protected readonly LinkGeneratorInterface $linkGenerator,
        protected PathInfoInterface $pathInfo,
        protected readonly string $imageStorageName,
    ) {}

    public function getStorageName(): string
    {
        return $this->imageStorageName;
    }

    public function link(): string
    {
        return $this->linkGenerator->link($this->pathInfo);
    }

    public function withNamespace(string $namespace): static
    {
        $info = clone $this;
        $info->pathInfo = $this->pathInfo->withNamespace($namespace);

        return $info;
    }

    public function withName(string $name): static
    {
        $info = clone $this;
        $info->pathInfo = $this->pathInfo->withName($name);

        return $info;
    }

    public function withExtension(?string $extension): static
    {
        $info = clone $this;
        $info->pathInfo = $this->pathInfo->withExtension($extension);

        return $info;
    }

    public function withExt(?string $extension): static
    {
        $info = clone $this;
        $info->pathInfo = $this->pathInfo->withExt($extension);

        return $info;
    }

    public function withVersion(?string $version): static
    {
        $info = clone $this;
        $info->pathInfo = $this->pathInfo->withVersion($version);

        return $info;
    }

    public function getNamespace(): string
    {
        return $this->pathInfo->getNamespace();
    }

    public function getName(): string
    {
        return $this->pathInfo->getName();
    }

    public function getExtension(): ?string
    {
        return $this->pathInfo->getExtension();
    }

    public function getVersion(): ?string
    {
        return $this->pathInfo->getVersion();
    }

    public function getPath(): string
    {
        return $this->pathInfo->getPath();
    }

    public function __toString(): string
    {
        return $this->link();
    }

    public function toArray(): array
    {
        return [
            'path' => $this->pathInfo->getPath(),
            'storage' => $this->getStorageName(),
            'version' => $this->pathInfo->getVersion(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
