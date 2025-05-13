<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Resource;

use Psr\Http\Message\StreamInterface;
use SixtyEightPublishers\FileStorage\Exception\FileNotFoundException;
use SixtyEightPublishers\FileStorage\Exception\FilesystemException;
use SixtyEightPublishers\FileStorage\PathInfoInterface;

interface ResourceFactoryInterface
{
    /**
     * @throws FileNotFoundException
     * @throws FilesystemException
     */
    public function createResource(PathInfoInterface $pathInfo): ResourceInterface;

    /**
     * @throws FileNotFoundException
     * @throws FilesystemException
     */
    public function createResourceFromFile(PathInfoInterface $pathInfo, string $filename): ResourceInterface;

    /**
     * @throws FileNotFoundException
     * @throws FilesystemException
     */
    public function createResourceFromPsrStream(PathInfoInterface $pathInfo, StreamInterface $stream): ResourceInterface;
}
