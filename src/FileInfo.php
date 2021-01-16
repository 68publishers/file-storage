<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage;

use SixtyEightPublishers\FileStorage\LinkGenerator\LinkGeneratorInterface;

class FileInfo implements FileInfoInterface
{
	/** @var \SixtyEightPublishers\FileStorage\LinkGenerator\LinkGeneratorInterface  */
	protected $linkGenerator;

	/** @var \SixtyEightPublishers\FileStorage\PathInfoInterface  */
	protected $pathInfo;

	/** @var string  */
	protected $imageStorageName;

	/**
	 * @param \SixtyEightPublishers\FileStorage\LinkGenerator\LinkGeneratorInterface $linkGenerator
	 * @param \SixtyEightPublishers\FileStorage\PathInfoInterface                    $pathInfo
	 * @param string                                                                 $imageStorageName
	 */
	public function __construct(LinkGeneratorInterface $linkGenerator, PathInfoInterface $pathInfo, string $imageStorageName)
	{
		$this->linkGenerator = $linkGenerator;
		$this->pathInfo = $pathInfo;
		$this->imageStorageName = $imageStorageName;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getStorageName(): string
	{
		return $this->imageStorageName;
	}

	/**
	 * {@inheritdoc}
	 */
	public function link(): string
	{
		return $this->linkGenerator->link($this->pathInfo);
	}

	/**
	 * {@inheritdoc}
	 */
	public function setNamespace(string $namespace): PathInfoInterface
	{
		$this->pathInfo->setNamespace($namespace);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setName(string $name): PathInfoInterface
	{
		$this->pathInfo->setName($name);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setExtension(?string $extension): PathInfoInterface
	{
		$this->pathInfo->setExtension($extension);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function withExt(string $extension)
	{
		return new static($this->linkGenerator, $this->pathInfo->withExt($extension), $this->imageStorageName);
	}

	/**
	 * {@inheritdoc}
	 */
	public function setVersion(?string $version): PathInfoInterface
	{
		$this->pathInfo->setVersion($version);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getNamespace(): string
	{
		return $this->pathInfo->getNamespace();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName(): string
	{
		return $this->pathInfo->getName();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getExtension(): ?string
	{
		return $this->pathInfo->getExtension();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getVersion(): ?string
	{
		return $this->pathInfo->getVersion();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPath(): string
	{
		return $this->pathInfo->getPath();
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toString(): string
	{
		return $this->link();
	}

	/**
	 * {@inheritdoc}
	 */
	public function jsonSerialize(): array
	{
		return [
			'path' => $this->pathInfo->getPath(),
			'storage' => $this->getStorageName(),
			'version' => $this->pathInfo->getVersion(),
		];
	}
}
