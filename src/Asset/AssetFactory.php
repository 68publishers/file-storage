<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Asset;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\FilesystemReader;
use League\Flysystem\StorageAttributes;
use function rtrim;
use function str_replace;
use function strlen;
use function substr;
use function trim;

final class AssetFactory implements AssetFactoryInterface
{
    /**
     * @throws FilesystemException
     */
    public function create(FilesystemOperator $localFilesystem, string $from, string $to): array
    {
        $to = trim($to, '\\/');

        if ($localFilesystem->fileExists($from)) {
            return [
                new Asset($from, $to),
            ];
        }

        $normalizedDirectory = str_replace('\\', '/', rtrim($from, '\\/')) . '/';

        /** @var array<AssetInterface> $assets */
        $assets = $localFilesystem->listContents($normalizedDirectory, FilesystemReader::LIST_DEEP)
            ->filter(static function (StorageAttributes $attributes) {
                return $attributes->isFile();
            })
            ->map(static function (StorageAttributes $attributes) use ($normalizedDirectory, $to) {
                $namespace = empty($to) ? '' : $to . '/';

                return new Asset($attributes->path(), $namespace . substr($attributes->path(), strlen($normalizedDirectory)));
            })
            ->toArray();

        return $assets;
    }
}
