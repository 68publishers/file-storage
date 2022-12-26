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
	) {
	}

	public function getStorageName(): string
	{
		return $this->imageStorageName;
	}

	public function link(): string
	{
		return $this->linkGenerator->link($this->pathInfo);
	}

	public function setNamespace(string $namespace): static
	{
		$this->pathInfo->setNamespace($namespace);

		return $this;
	}

	public function setName(string $name): static
	{
		$this->pathInfo->setName($name);

		return $this;
	}

	public function setExtension(?string $extension): static
	{
		$this->pathInfo->setExtension($extension);

		return $this;
	}

	public function withExt(string $extension): static
	{
		$info = clone $this;
		$info->pathInfo = $this->pathInfo->withExt($extension);

		return $info;
	}

	public function setVersion(?string $version): static
	{
		$this->pathInfo->setVersion($version);

		return $this;
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

	public function jsonSerialize(): array
	{
		return [
			'path' => $this->pathInfo->getPath(),
			'storage' => $this->getStorageName(),
			'version' => $this->pathInfo->getVersion(),
		];
	}
}
