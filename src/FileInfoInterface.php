<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage;

use JsonSerializable;

interface FileInfoInterface extends PathInfoInterface, JsonSerializable
{
	public function getStorageName(): string;

	public function link(): string;

	/**
	 * @return array{path: string, storage: string, version: ?string}
	 */
	public function jsonSerialize(): array;
}
