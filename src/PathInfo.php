<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage;

use SixtyEightPublishers\FileStorage\Exception\PathInfoException;
use function sprintf;

class PathInfo implements PathInfoInterface
{
	private string $namespace;

	private string $name;

	private ?string $extension = null;

	private ?string $version = null;

	/**
	 * @throws \SixtyEightPublishers\FileStorage\Exception\PathInfoException
	 */
	public function __construct(string $namespace, string $name, ?string $extension, ?string $version = null)
	{
		$this->setNamespace($namespace);
		$this->setName($name);
		$this->setExtension($extension);
		$this->setVersion($version);
	}

	public function setNamespace(string $namespace): static
	{
		$this->namespace = $namespace;

		return $this;
	}

	/**
	 * @throws \SixtyEightPublishers\FileStorage\Exception\PathInfoException
	 */
	public function setName(string $name): static
	{
		if ($name === '') {
			throw PathInfoException::invalidPath($name);
		}

		$this->name = $name;

		return $this;
	}

	public function setExtension(?string $extension): static
	{
		$this->extension = $extension;

		return $this;
	}

	public function withExt(string $extension): static
	{
		$info = clone $this;
		$info->setExtension($extension);

		return $info;
	}

	public function setVersion(?string $version): static
	{
		$this->version = $version;

		return $this;
	}

	public function getNamespace(): string
	{
		return $this->namespace;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getExtension(): ?string
	{
		return $this->extension;
	}

	public function getVersion(): ?string
	{
		return $this->version;
	}

	public function getPath(): string
	{
		$namespace = $this->getNamespace();

		return $namespace === ''
			? $this->getName()
			: sprintf('%s/%s%s', $namespace, $this->getName(), null === $this->getExtension() ? '' : '.' . $this->getExtension());
	}

	public function __toString(): string
	{
		return $this->getPath();
	}
}
