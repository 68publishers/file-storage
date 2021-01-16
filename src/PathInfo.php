<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage;

use SixtyEightPublishers\FileStorage\Exception\PathInfoException;

class PathInfo implements PathInfoInterface
{
	/** @var string  */
	private $namespace;

	/** @var string  */
	private $name;

	/** @var string|NULL */
	private $extension;

	/** @var string|NULL */
	private $version;

	/**
	 * @param string      $namespace
	 * @param string      $name
	 * @param string|NULL $extension
	 * @param string|NULL $version
	 *
	 * @throws \SixtyEightPublishers\FileStorage\Exception\PathInfoException
	 */
	public function __construct(string $namespace, string $name, ?string $extension, ?string $version = NULL)
	{
		$this->setNamespace($namespace);
		$this->setName($name);
		$this->setExtension($extension);
		$this->setVersion($version);
	}

	/**
	 * {@inheritDoc}
	 */
	public function setNamespace(string $namespace): PathInfoInterface
	{
		$this->namespace = $namespace;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \SixtyEightPublishers\FileStorage\Exception\PathInfoException
	 */
	public function setName(string $name): PathInfoInterface
	{
		if ($name === '') {
			throw PathInfoException::invalidPath($name);
		}

		$this->name = $name;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setExtension(?string $extension): PathInfoInterface
	{
		$this->extension = $extension;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \SixtyEightPublishers\FileStorage\Exception\PathInfoException
	 */
	public function withExt(string $extension)
	{
		return new static($this->getNamespace(), $this->getName(), $extension, $this->getVersion());
	}

	/**
	 * {@inheritDoc}
	 */
	public function setVersion(?string $version): PathInfoInterface
	{
		$this->version = $version;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getNamespace(): string
	{
		return $this->namespace;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getExtension(): ?string
	{
		return $this->extension;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getVersion(): ?string
	{
		return $this->version;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPath(): string
	{
		$namespace = $this->getNamespace();

		return $namespace === ''
			? $this->getName()
			: sprintf('%s/%s%s', $namespace, $this->getName(), NULL === $this->getExtension() ? '' : '.' . $this->getExtension());
	}

	/**
	 * {@inheritDoc}
	 */
	public function __toString(): string
	{
		return $this->getPath();
	}
}
