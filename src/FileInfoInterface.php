<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage;

use JsonSerializable;

interface FileInfoInterface extends PathInfoInterface, JsonSerializable
{
    public function getStorageName(): string;

    public function link(bool $absolute = true): string;

    /**
     * @return array{path: string, storage: string, version: ?string}
     */
    public function toArray(): array;

    /**
     * @return array{path: string, storage: string, version: ?string}
     */
    public function jsonSerialize(): array;
}
