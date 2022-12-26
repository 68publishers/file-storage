<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Asset;

use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use League\Flysystem\FilesystemOperator;
use SixtyEightPublishers\FileStorage\FileStorageInterface;
use function sprintf;

final class AssetsCopier implements AssetsCopierInterface
{
	public function __construct(
		private readonly FilesystemOperator $localFilesystemOperator,
		private readonly PathsProviderInterface $pathsProvider,
		private readonly AssetFactoryInterface $assetFactory
	) {
	}

	/**
	 * @throws \SixtyEightPublishers\FileStorage\Exception\FileNotFoundException
	 * @throws \SixtyEightPublishers\FileStorage\Exception\FilesystemException
	 */
	public function copy(FileStorageInterface $fileStorage, ?LoggerInterface $logger = null): void
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
