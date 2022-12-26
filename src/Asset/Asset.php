<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Asset;

final class Asset implements AssetInterface
{
	public function __construct(
		private readonly string $sourceRealPath,
		private readonly string $outputPath
	) {
	}

	public function getSourceRealPath(): string
	{
		return $this->sourceRealPath;
	}

	public function getOutputPath(): string
	{
		return $this->outputPath;
	}
}
