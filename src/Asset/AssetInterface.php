<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Asset;

interface AssetInterface
{
	/**
	 * @return string
	 */
	public function getSourceRealPath(): string;

	/**
	 * @return string
	 */
	public function getOutputPath(): string;
}
