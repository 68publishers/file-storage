<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Resource;

use GuzzleHttp\Psr7\BufferStream;
use GuzzleHttp\Psr7\Stream;
use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\Flysystem\UnableToReadFile;
use Mockery;
use SixtyEightPublishers\FileStorage\Exception\FileNotFoundException;
use SixtyEightPublishers\FileStorage\Exception\FilesystemException;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use SixtyEightPublishers\FileStorage\Resource\ResourceFactory;
use Tester\Assert;
use Tester\TestCase;
use function fclose;
use function fopen;
use function is_resource;
use function stream_get_contents;

require __DIR__ . '/../bootstrap.php';

final class ResourceFactoryTest extends TestCase
{
    public function testExceptionShouldBeThrownIfFileIsMissing(): void
    {
        $pathInfo = Mockery::mock(PathInfoInterface::class);

        $pathInfo->shouldReceive('getPath')
            ->once()
            ->andReturns('var/www/file.json');

        $resourceFactory = $this->createResourceFactory([]);

        Assert::exception(
            static fn () => $resourceFactory->createResource($pathInfo),
            FileNotFoundException::class,
            'File "var/www/file.json" not found.',
        );
    }

    public function testExceptionShouldBeThrownIfFilesystemExceptionIsThrown(): void
    {
        $filename = 'var/www/file.json';
        $pathInfo = Mockery::mock(PathInfoInterface::class);

        $pathInfo->shouldReceive('getPath')
            ->once()
            ->andReturns($filename);

        $adapter = new InMemoryFilesystemAdapter();
        $adapter = Mockery::instanceMock($adapter);

        $adapter->shouldReceive('readStream')
            ->once()
            ->with($filename)
            ->andThrows(UnableToReadFile::fromLocation($filename, 'test'));

        $resourceFactory = $this->createResourceFactory([
            $filename => '{}',
        ], $adapter);

        Assert::exception(
            static fn () => $resourceFactory->createResource($pathInfo),
            FilesystemException::class,
            'Can not read stream from file "var/www/file.json".',
        );
    }

    public function testResourceShouldBeCreated(): void
    {
        $filename = 'var/www/file.json';
        $pathInfo = Mockery::mock(PathInfoInterface::class);

        $pathInfo->shouldReceive('getPath')
            ->once()
            ->andReturns($filename);

        $resourceFactory = $this->createResourceFactory([
            $filename => '{}',
        ]);

        $resource = $resourceFactory->createResource($pathInfo);

        try {
            Assert::same($pathInfo, $resource->getPathInfo());
            Assert::true(is_resource($resource->getSource()));
            Assert::same('{}', stream_get_contents($resource->getSource()));
            Assert::same('application/json', $resource->getMimeType());
            Assert::same(2, $resource->getFilesize());
        } finally {
            fclose($resource->getSource());
        }
    }

    public function testExceptionShouldBeThrownIfLocalFileIsMissing(): void
    {
        $pathInfo = Mockery::mock(PathInfoInterface::class);
        $resourceFactory = $this->createResourceFactory();

        Assert::exception(
            static fn () => $resourceFactory->createResourceFromFile($pathInfo, __DIR__ . '/missing.json'),
            FileNotFoundException::class,
            'File "%A?%missing.json" not found.',
        );
    }

    public function testExceptionShouldBeThrownIfLocalFileIsNotReadable(): void
    {
        $pathInfo = Mockery::mock(PathInfoInterface::class);
        $resourceFactory = $this->createResourceFactory();

        touch(__DIR__ . '/file.nonReadable.json');
        chmod(__DIR__ . '/file.nonReadable.json', 600);

        try {
            Assert::exception(
                static fn () => $resourceFactory->createResourceFromFile($pathInfo, __DIR__ . '/file.nonReadable.json'),
                FilesystemException::class,
                'Can not read stream from file "%A?%/file.nonReadable.json". %A?%',
            );
        } finally {
            @unlink(__DIR__ . '/file.nonReadable.json');
        }
    }

    public function testResourceFromLocalFileShouldBeCreated(): void
    {
        $pathInfo = Mockery::mock(PathInfoInterface::class);
        $resourceFactory = $this->createResourceFactory();
        $resource = $resourceFactory->createResourceFromFile($pathInfo, __DIR__ . '/file.json');

        try {
            Assert::same($pathInfo, $resource->getPathInfo());
            Assert::true(is_resource($resource->getSource()));
            Assert::same("{\"abc\":123}\n", stream_get_contents($resource->getSource()));
            Assert::same('application/json', $resource->getMimeType());
            Assert::same(12, $resource->getFilesize());
        } finally {
            fclose($resource->getSource());
        }
    }

    public function testResourceFromFileBasedPsrStreamShouldBeCreated(): void
    {
        $pathInfo = Mockery::mock(PathInfoInterface::class);
        $resourceFactory = $this->createResourceFactory();

        $handle = fopen(__DIR__ . '/file.json', 'r+');
        $stream = new Stream($handle);

        try {
            $resource = $resourceFactory->createResourceFromPsrStream($pathInfo, $stream);

            Assert::same($pathInfo, $resource->getPathInfo());
            Assert::true(is_resource($resource->getSource()));
            Assert::same("{\"abc\":123}\n", stream_get_contents($resource->getSource()));
            Assert::same('application/json', $resource->getMimeType());
            Assert::same(12, $resource->getFilesize());
            Assert::same($handle, $resource->getSource());
        } finally {
            fclose($handle);
        }
    }

    public function testResourceFromBufferPsrStreamShouldBeCreated(): void
    {
        $pathInfo = Mockery::mock(PathInfoInterface::class);
        $resourceFactory = $this->createResourceFactory();

        $stream = new BufferStream();
        $stream->write("{\"abc\":123}\n");
        $resource = $resourceFactory->createResourceFromPsrStream($pathInfo, $stream);

        try {
            Assert::same($pathInfo, $resource->getPathInfo());
            Assert::true(is_resource($resource->getSource()));
            Assert::same("{\"abc\":123}\n", stream_get_contents($resource->getSource()));
            Assert::same('application/json', $resource->getMimeType());
            Assert::same(12, $resource->getFilesize());
        } finally {
            fclose($resource->getSource());
        }
    }

    private function createResourceFactory(array $files = [], ?InMemoryFilesystemAdapter $adapter = null): ResourceFactory
    {
        $fs = new Filesystem(
            $adapter ?? new InMemoryFilesystemAdapter(),
        );

        foreach ($files as $filename => $content) {
            $fs->write($filename, $content);
        }

        return new ResourceFactory($fs);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

(new ResourceFactoryTest())->run();
