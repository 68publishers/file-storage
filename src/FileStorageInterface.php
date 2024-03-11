<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage;

use SixtyEightPublishers\FileStorage\Config\ConfigInterface;
use SixtyEightPublishers\FileStorage\LinkGenerator\LinkGeneratorInterface;
use SixtyEightPublishers\FileStorage\Persistence\FilePersisterInterface;
use SixtyEightPublishers\FileStorage\Resource\ResourceFactoryInterface;

interface FileStorageInterface extends ResourceFactoryInterface, LinkGeneratorInterface, FilePersisterInterface
{
    public function getName(): string;

    public function getConfig(): ConfigInterface;

    public function createPathInfo(string $path): PathInfoInterface;

    public function createFileInfo(PathInfoInterface $pathInfo): FileInfoInterface;
}
