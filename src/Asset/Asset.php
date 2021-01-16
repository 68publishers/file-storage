<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Asset;

final class Asset implements AssetInterface
{
	/** @var string  */
	private $sourceRealPath;

	/** @var string  */
	private $outputPath;

	/**
	 * @param string $sourceRealPath
	 * @param string $outputPath
	 */
	public function __construct(string $sourceRealPath, string $outputPath)
	{
		$this->sourceRealPath = $sourceRealPath;
		$this->outputPath = $outputPath;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSourceRealPath(): string
	{
		return $this->sourceRealPath;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getOutputPath(): string
	{
		return $this->outputPath;
	}
}
