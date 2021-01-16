<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage;

interface PathInfoInterface
{
	/**
	 * @param string $namespace
	 *
	 * @return \SixtyEightPublishers\FileStorage\PathInfoInterface|$this
	 */
	public function setNamespace(string $namespace): self;

	/**
	 * @param string $name
	 *
	 * @return \SixtyEightPublishers\FileStorage\PathInfoInterface|$this
	 */
	public function setName(string $name): self;

	/**
	 * @param string|NULL $extension
	 *
	 * @return \SixtyEightPublishers\FileStorage\PathInfoInterface|$this
	 */
	public function setExtension(?string $extension): self;

	/**
	 * @param string $extension
	 *
	 * @return $this
	 */
	public function withExt(string $extension);

	/**
	 * @param string|NULL $version
	 *
	 * @return \SixtyEightPublishers\FileStorage\PathInfoInterface|$this
	 */
	public function setVersion(?string $version): self;

	/**
	 * @return string
	 */
	public function getNamespace(): string;

	/**
	 * @return string
	 */
	public function getName(): string;

	/**
	 * @return NULL|string
	 */
	public function getExtension(): ?string;

	/**
	 * @return NULL|string
	 */
	public function getVersion(): ?string;

	/**
	 * @return string
	 */
	public function getPath(): string;

	/**
	 * Calls ::getPath() internally.
	 *
	 * @return string
	 */
	public function __toString(): string;
}
