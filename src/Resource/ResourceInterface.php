<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Resource;

use SixtyEightPublishers\FileStorage\PathInfoInterface;

interface ResourceInterface
{
	/**
	 * @return \SixtyEightPublishers\FileStorage\PathInfoInterface
	 */
	public function getPathInfo(): PathInfoInterface;

	/**
	 * @return mixed
	 */
	public function getSource();

	/**
	 * @param \SixtyEightPublishers\FileStorage\PathInfoInterface $pathInfo
	 *
	 * @return $this
	 */
	public function withPathInfo(PathInfoInterface $pathInfo);
}
