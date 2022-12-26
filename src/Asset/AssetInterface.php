<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Asset;

interface AssetInterface
{
	public function getSourceRealPath(): string;

	public function getOutputPath(): string;
}
