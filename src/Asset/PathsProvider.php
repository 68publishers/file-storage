<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Asset;

final class PathsProvider implements PathsProviderInterface
{
	/** @var array  */
	private $pathsMap = [];

	/**
	 * @param string $name
	 * @param array  $paths
	 */
	public function addPaths(string $name, array $paths): void
	{
		$this->pathsMap[$name] = array_merge($this->pathsMap[$name] ?? [], $paths);
	}

	/**
	 * @param string $name
	 *
	 * @return array
	 */
	public function getPaths(string $name): array
	{
		return array_key_exists($name, $this->pathsMap) ? $this->pathsMap[$name] : [];
	}
}
