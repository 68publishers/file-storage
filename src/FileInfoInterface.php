<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage;

use JsonSerializable;

interface FileInfoInterface extends PathInfoInterface, JsonSerializable
{
	/**
	 * @return string
	 */
	public function getStorageName(): string;

	/**
	 * @return string
	 */
	public function link(): string;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array;
}
