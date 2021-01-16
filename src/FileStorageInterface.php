<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage;

use SixtyEightPublishers\FileStorage\Config\ConfigInterface;
use SixtyEightPublishers\FileStorage\Resource\ResourceFactoryInterface;
use SixtyEightPublishers\FileStorage\Persistence\FilePersisterInterface;
use SixtyEightPublishers\FileStorage\LinkGenerator\LinkGeneratorInterface;

interface FileStorageInterface extends ResourceFactoryInterface, LinkGeneratorInterface, FilePersisterInterface
{
	/**
	 * @return string
	 */
	public function getName(): string;

	/**
	 * @return \SixtyEightPublishers\FileStorage\Config\ConfigInterface
	 */
	public function getConfig(): ConfigInterface;

	/**
	 * @param string $path
	 *
	 * @return \SixtyEightPublishers\FileStorage\PathInfoInterface
	 */
	public function createPathInfo(string $path): PathInfoInterface;

	/**
	 * @param \SixtyEightPublishers\FileStorage\PathInfoInterface $pathInfo
	 *
	 * @return \SixtyEightPublishers\FileStorage\FileInfoInterface
	 */
	public function createFileInfo(PathInfoInterface $pathInfo): FileInfoInterface;
}
