<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage;

interface PathInfoInterface
{
	public function setNamespace(string $namespace): static;

	public function setName(string $name): static;

	public function setExtension(?string $extension): static;

	public function withExt(string $extension): static;

	public function setVersion(?string $version): static;

	public function getNamespace(): string;

	public function getName(): string;

	public function getExtension(): ?string;

	public function getVersion(): ?string;

	public function getPath(): string;

	/**
	 * Calls ::getPath() internally.
	 */
	public function __toString(): string;
}
