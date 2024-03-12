<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Asset;

use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SixtyEightPublishers\FileStorage\Exception\FileNotFoundException;
use SixtyEightPublishers\FileStorage\Exception\FilesystemException;
use SixtyEightPublishers\FileStorage\FileStorageInterface;
use function sprintf;

final class AssetsCopier implements AssetsCopierInterface
{
    public function __construct(
        private readonly FilesystemOperator $localFilesystemOperator,
        private readonly PathsProviderInterface $pathsProvider,
        private readonly AssetFactoryInterface $assetFactory,
    ) {}

    /**
     * @throws FileNotFoundException
     * @throws FilesystemException
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
                    $asset->getOutputPath(),
                ));

                $this->copyAsset($asset, $fileStorage);
            }
        }
    }

    /**
     * @throws FileNotFoundException
     * @throws FilesystemException
     */
    private function copyAsset(AssetInterface $asset, FileStorageInterface $fileStorage): void
    {
        $resource = $fileStorage->createResourceFromFile(
            $fileStorage->createPathInfo($asset->getOutputPath()),
            $asset->getSourceRealPath(),
        );

        $fileStorage->save($resource);
    }
}
