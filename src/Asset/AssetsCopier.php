<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Asset;

use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use League\Flysystem\FilesystemOperator;
use SixtyEightPublishers\FileStorage\FileStorageInterface;

final class AssetsCopier implements AssetsCopierInterface
{
	/** @var \League\Flysystem\FilesystemOperator  */
	private $localFilesystemOperator;

	/** @var \SixtyEightPublishers\FileStorage\Asset\PathsProviderInterface  */
	private $pathsProvider;

	/** @var \SixtyEightPublishers\FileStorage\Asset\AssetFactoryInterface  */
	private $assetFactory;

	/**
	 * @param \League\Flysystem\FilesystemOperator                           $localFilesystemOperator
	 * @param \SixtyEightPublishers\FileStorage\Asset\PathsProviderInterface $pathsProvider
	 * @param \SixtyEightPublishers\FileStorage\Asset\AssetFactoryInterface  $assetFactory
	 */
	public function __construct(FilesystemOperator $localFilesystemOperator, PathsProviderInterface $pathsProvider, AssetFactoryInterface $assetFactory)
	{
		$this->localFilesystemOperator = $localFilesystemOperator;
		$this->pathsProvider = $pathsProvider;
		$this->assetFactory = $assetFactory;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \SixtyEightPublishers\FileStorage\Exception\FileNotFoundException
	 * @throws \SixtyEightPublishers\FileStorage\Exception\FilesystemException
	 */
	public function copy(FileStorageInterface $fileStorage, ?LoggerInterface $logger = NULL): void
	{
		$logger = $logger ?? new NullLogger();

		foreach ($this->pathsProvider->getPaths($fileStorage->getName()) as $from => $to) {
			foreach ($this->assetFactory->create($this->localFilesystemOperator, $from, $to) as $asset) {
				$logger->info(sprintf(
					'Copying %s to %s://%s',
					$asset->getSourceRealPath(),
					$fileStorage->getName(),
					$asset->getOutputPath()
				));

				$this->copyAsset($asset, $fileStorage);
			}
		}
	}

	/**
	 * @param \SixtyEightPublishers\FileStorage\Asset\AssetInterface $asset
	 * @param \SixtyEightPublishers\FileStorage\FileStorageInterface $fileStorage
	 *
	 * @throws \SixtyEightPublishers\FileStorage\Exception\FileNotFoundException
	 * @throws \SixtyEightPublishers\FileStorage\Exception\FilesystemException
	 */
	private function copyAsset(AssetInterface $asset, FileStorageInterface $fileStorage): void
	{
		$resource = $fileStorage->createResourceFromLocalFile(
			$fileStorage->createPathInfo($asset->getOutputPath()),
			$asset->getSourceRealPath()
		);

		$fileStorage->save($resource);
	}
}
