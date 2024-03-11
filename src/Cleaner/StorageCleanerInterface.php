<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Cleaner;

use League\Flysystem\FilesystemOperator;

interface StorageCleanerInterface
{
    public const OPTION_NAMESPACE = 'namespace';
    public const OPTION_FILESYSTEM_PREFIX = 'filesystem-prefix';

    /**
     * @param array<string, mixed> $options
     */
    public function getCount(FilesystemOperator $filesystemOperator, array $options = []): int;

    /**
     * @param array<string, mixed> $options
     */
    public function clean(FilesystemOperator $filesystemOperator, array $options = []): void;
}
