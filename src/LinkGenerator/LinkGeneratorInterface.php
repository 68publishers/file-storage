<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\LinkGenerator;

use SixtyEightPublishers\FileStorage\PathInfoInterface;

interface LinkGeneratorInterface
{
	/**
	 * @param \SixtyEightPublishers\FileStorage\PathInfoInterface $pathInfo
	 *
	 * @return string
	 */
	public function link(PathInfoInterface $pathInfo): string;
}
