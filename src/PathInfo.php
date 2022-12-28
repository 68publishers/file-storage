<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage;

use SixtyEightPublishers\FileStorage\Exception\PathInfoException;
use function sprintf;

class PathInfo implements PathInfoInterface
{
	/**
	 * @throws \SixtyEightPublishers\FileStorage\Exception\PathInfoException
	 */
	public function __construct(
		private string $namespace,
		private string $name,
		private ?string $extension,
		private ?string $version = null
	) {
		$this->validateName($name);
	}

	public function withNamespace(string $namespace): static
	{
		$info = clone $this;
		$info->namespace = $namespace;

		return $info;
	}

	/**
	 * @throws \SixtyEightPublishers\FileStorage\Exception\PathInfoException
	 */
	public function withName(string $name): static
	{
		$this->validateName($name);

		$info = clone $this;
		$info->name = $name;

		return $info;
	}

	public function withExtension(?string $extension): static
	{
		$info = clone $this;
		$info->extension = $extension;

		return $info;
	}

	public function withExt(?string $extension): static
	{
		return $this->withExtension($extension);
	}

	public function withVersion(?string $version): static
	{
		$info = clone $this;
		$info->version = $version;

		return $info;
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
		return $this->createPath();
	}

	public function __toString(): string
	{
		return $this->getPath();
	}

	/**
	 * @throws \SixtyEightPublishers\FileStorage\Exception\PathInfoException
	 */
	private function validateName(string $name): void
	{
		if ($name === '') {
			throw PathInfoException::invalidPath($this->createPath($name));
		}
	}

	private function createPath(?string $name = null): string
	{
		$name = $name ?? $this->getName();
		$namespace = $this->getNamespace();

		return $namespace === ''
			? sprintf('%s%s', $name, null === $this->getExtension() ? '' : '.' . $this->getExtension())
			: sprintf('%s/%s%s', $namespace, $name, null === $this->getExtension() ? '' : '.' . $this->getExtension());
	}
}
