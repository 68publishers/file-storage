<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Resource;

use SixtyEightPublishers\FileStorage\PathInfoInterface;

interface ResourceFactoryInterface
{
	/**
	 * @param \SixtyEightPublishers\FileStorage\PathInfoInterface $pathInfo
	 *
	 * @return \SixtyEightPublishers\FileStorage\Resource\ResourceInterface
	 * @throws \SixtyEightPublishers\FileStorage\Exception\FileNotFoundException
	 * @throws \SixtyEightPublishers\FileStorage\Exception\FilesystemException
	 */
	public function createResource(PathInfoInterface $pathInfo): ResourceInterface;

	/**
	 * @param \SixtyEightPublishers\FileStorage\PathInfoInterface $pathInfo
	 * @param string                                              $filename
	 *
	 * @return \SixtyEightPublishers\FileStorage\Resource\ResourceInterface
	 * @throws \SixtyEightPublishers\FileStorage\Exception\FileNotFoundException
	 * @throws \SixtyEightPublishers\FileStorage\Exception\FilesystemException
	 */
	public function createResourceFromLocalFile(PathInfoInterface $pathInfo, string $filename): ResourceInterface;
}
