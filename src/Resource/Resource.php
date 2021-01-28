<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Resource;

use SixtyEightPublishers\FileStorage\PathInfoInterface;

class Resource implements ResourceInterface
{
	/** @var \SixtyEightPublishers\FileStorage\PathInfoInterface  */
	private $pathInfo;

	/** @var \resource  */
	private $source;

	/**
	 * @param \SixtyEightPublishers\FileStorage\PathInfoInterface $pathInfo
	 * @param \resource                                           $source
	 */
	public function __construct(PathInfoInterface $pathInfo, $source)
	{
		$this->pathInfo = $pathInfo;
		$this->source = $source;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPathInfo(): PathInfoInterface
	{
		return $this->pathInfo;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSource()
	{
		return $this->source;
	}

	/**
	 * {@inheritDoc}
	 */
	public function withPathInfo(PathInfoInterface $pathInfo): self
	{
		return new static($pathInfo, $this->getSource());
	}
}
