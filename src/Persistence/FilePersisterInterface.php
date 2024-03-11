<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Persistence;

use League\Flysystem\FilesystemOperator;
use SixtyEightPublishers\FileStorage\Exception\FilesystemException;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use SixtyEightPublishers\FileStorage\Resource\ResourceInterface;

interface FilePersisterInterface
{
    public const OPTION_SUPPRESS_EXCEPTIONS = '68.suppress_exceptions';

    public function getFilesystem(): FilesystemOperator;

    public function exists(PathInfoInterface $pathInfo): bool;

    /**
     * Returns path of stored image
     *
     * @param array<string, mixed> $config
     *
     * @throws FilesystemException
     */
    public function save(ResourceInterface $resource, array $config = []): string;

    /**
     * @param array<string, mixed> $config
     *
     * @throws FilesystemException
     */
    public function delete(PathInfoInterface $pathInfo, array $config = []): void;
}
