<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Resource;

use Mockery;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use SixtyEightPublishers\FileStorage\Resource\StreamResource;
use Tester\Assert;
use Tester\TestCase;
use function fclose;
use function fopen;

require __DIR__ . '/../bootstrap.php';

final class StreamResourceTest extends TestCase
{
    public function testSimpleStreamResource(): void
    {
        $pathInfo = Mockery::mock(PathInfoInterface::class);
        $pathInfo2 = Mockery::mock(PathInfoInterface::class);
        $source = fopen('php://temp', 'r+');

        $resource = new StreamResource(
            pathInfo: $pathInfo,
            source: $source,
            mimeType: null,
            filesize: null,
        );

        $newResource = $resource->withPathInfo($pathInfo2);

        Assert::notSame($resource, $newResource);

        Assert::same($pathInfo, $resource->getPathInfo());
        Assert::same($pathInfo2, $newResource->getPathInfo());

        Assert::same($source, $resource->getSource());
        Assert::same($source, $newResource->getSource());

        Assert::null($resource->getMimeType());
        Assert::null($newResource->getMimeType());

        Assert::null($resource->getFilesize());
        Assert::null($newResource->getFilesize());

        fclose($source);
    }

    public function testStreamResourceWithScalarMimeTypeAndFilesize(): void
    {
        $pathInfo = Mockery::mock(PathInfoInterface::class);
        $pathInfo2 = Mockery::mock(PathInfoInterface::class);
        $source = fopen('php://temp', 'r+');

        $resource = new StreamResource(
            pathInfo: $pathInfo,
            source: $source,
            mimeType: 'application/pdf',
            filesize: 131380,
        );

        $newResource = $resource->withPathInfo($pathInfo2);

        Assert::same('application/pdf', $resource->getMimeType());
        Assert::same('application/pdf', $newResource->getMimeType());

        Assert::same(131380, $resource->getFilesize());
        Assert::same(131380, $newResource->getFilesize());

        fclose($source);
    }

    public function testStreamResourceWithClosureMimeTypeAndFilesize(): void
    {
        $pathInfo = Mockery::mock(PathInfoInterface::class);
        $source = fopen('php://temp', 'r+');

        $mimeTypeClosureCounter = 0;
        $filesizeClosureCounter = 0;

        $mimeTypeClosure = function () use (&$mimeTypeClosureCounter): string {
            ++$mimeTypeClosureCounter;

            return 'application/pdf';
        };

        $filesizeClosure = function () use (&$filesizeClosureCounter): int {
            ++$filesizeClosureCounter;

            return 131380;
        };

        $resource = new StreamResource(
            pathInfo: $pathInfo,
            source: $source,
            mimeType: $mimeTypeClosure,
            filesize: $filesizeClosure,
        );

        Assert::same('application/pdf', $resource->getMimeType());
        Assert::same(131380, $resource->getFilesize());

        $resource->getMimeType();
        $resource->getFilesize();

        Assert::same(1, $mimeTypeClosureCounter);
        Assert::same(1, $filesizeClosureCounter);

        fclose($source);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

(new StreamResourceTest())->run();
