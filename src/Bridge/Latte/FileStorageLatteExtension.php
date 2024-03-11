<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Latte;

use Latte\Extension;
use SixtyEightPublishers\FileStorage\FileInfoInterface;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;
use SixtyEightPublishers\FileStorage\PathInfoInterface;

final class FileStorageLatteExtension extends Extension
{
    public function __construct(
        private readonly FileStorageProviderInterface $fileStorageProvider,
    ) {}

    public function getFunctions(): array
    {
        return [
            'file_info' => fn (PathInfoInterface|string $pathInfo, ?string $storageName = null) => $this->createFileInfo($pathInfo, $storageName),
        ];
    }

    private function createFileInfo(PathInfoInterface|string $pathInfo, ?string $storageName = null): FileInfoInterface
    {
        if ($pathInfo instanceof FileInfoInterface) {
            return $pathInfo;
        }

        $storage = $this->fileStorageProvider->get($storageName);

        if (!$pathInfo instanceof PathInfoInterface) {
            $pathInfo = $storage->createPathInfo((string) $pathInfo);
        }

        return $storage->createFileInfo($pathInfo);
    }
}
