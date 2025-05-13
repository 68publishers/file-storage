<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage;

use League\Flysystem\FilesystemOperator;
use Psr\Http\Message\StreamInterface;
use SixtyEightPublishers\FileStorage\Config\ConfigInterface;
use SixtyEightPublishers\FileStorage\Exception\PathInfoException;
use SixtyEightPublishers\FileStorage\Helper\Path;
use SixtyEightPublishers\FileStorage\LinkGenerator\LinkGeneratorInterface;
use SixtyEightPublishers\FileStorage\Persistence\FilePersisterInterface;
use SixtyEightPublishers\FileStorage\Resource\ResourceFactoryInterface;
use SixtyEightPublishers\FileStorage\Resource\ResourceInterface;

class FileStorage implements FileStorageInterface
{
    public function __construct(
        protected readonly string $name,
        protected readonly ConfigInterface $config,
        protected readonly ResourceFactoryInterface $resourceFactory,
        protected readonly LinkGeneratorInterface $linkGenerator,
        protected readonly FilePersisterInterface $filePersister,
    ) {}

    public function getFilesystem(): FilesystemOperator
    {
        return $this->filePersister->getFilesystem();
    }

    public function exists(PathInfoInterface $pathInfo): bool
    {
        return $this->filePersister->exists($pathInfo);
    }

    public function save(ResourceInterface $resource, array $config = []): string
    {
        return $this->filePersister->save($resource, $config);
    }

    public function delete(PathInfoInterface $pathInfo, array $config = []): void
    {
        $this->filePersister->delete($pathInfo, $config);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    /**
     * @throws PathInfoException
     */
    public function createPathInfo(string $path): PathInfoInterface
    {
        return new PathInfo(...Path::parse($path));
    }

    public function createFileInfo(PathInfoInterface $pathInfo): FileInfoInterface
    {
        return new FileInfo($this->linkGenerator, $pathInfo, $this->getName());
    }

    public function link(PathInfoInterface $pathInfo): string
    {
        return $this->linkGenerator->link($pathInfo);
    }

    public function createResource(PathInfoInterface $pathInfo): ResourceInterface
    {
        return $this->resourceFactory->createResource($pathInfo);
    }

    public function createResourceFromFile(PathInfoInterface $pathInfo, string $filename): ResourceInterface
    {
        return $this->resourceFactory->createResourceFromFile($pathInfo, $filename);
    }

    public function createResourceFromPsrStream(PathInfoInterface $pathInfo, StreamInterface $stream): ResourceInterface
    {
        return $this->resourceFactory->createResourceFromPsrStream($pathInfo, $stream);
    }
}
