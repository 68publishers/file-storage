<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Resource;

use ArrayObject;
use Mockery;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use SixtyEightPublishers\FileStorage\Resource\SimpleResource;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

final class SimpleResourceTest extends TestCase
{
    public function testSimpleResource(): void
    {
        $pathInfo = Mockery::mock(PathInfoInterface::class);
        $source = new ArrayObject();
        $resource = new SimpleResource($pathInfo, $source);

        $pathInfo2 = Mockery::mock(PathInfoInterface::class);
        $newResource = $resource->withPathInfo($pathInfo2);

        Assert::notSame($resource, $newResource);
        Assert::same($pathInfo, $resource->getPathInfo());
        Assert::same($pathInfo2, $newResource->getPathInfo());
        Assert::same($source, $resource->getSource());
        Assert::same($source, $newResource->getSource());
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

(new SimpleResourceTest())->run();
