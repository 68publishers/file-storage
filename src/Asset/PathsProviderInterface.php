<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Asset;

interface PathsProviderInterface
{
	/**
	 * @param string $name
	 *
	 * @return array
	 */
	public function getPaths(string $name): array;
}
