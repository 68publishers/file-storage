<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage;

interface PathInfoInterface
{
	public function withNamespace(string $namespace): static;

	public function withName(string $name): static;

	public function withExtension(?string $extension): static;

	/**
	 * Alias for withExtension()
	 */
	public function withExt(?string $extension): static;

	public function withVersion(?string $version): static;

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
