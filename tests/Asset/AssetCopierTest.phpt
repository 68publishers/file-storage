<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Asset;

use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use Mockery;
use Psr\Log\Test\TestLogger;
use SixtyEightPublishers\FileStorage\Asset\Asset;
use SixtyEightPublishers\FileStorage\Asset\AssetFactoryInterface;
use SixtyEightPublishers\FileStorage\Asset\AssetsCopier;
use SixtyEightPublishers\FileStorage\Asset\PathsProviderInterface;
use SixtyEightPublishers\FileStorage\FileStorageInterface;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use SixtyEightPublishers\FileStorage\Resource\SimpleResource;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

final class AssetCopierTest extends TestCase
{
    public function testAssetsShouldBeCopied(): void
    {
        $filesystem = new Filesystem(new InMemoryFilesystemAdapter());
        $pathsProvider = Mockery::mock(PathsProviderInterface::class);

        $pathsProvider->shouldReceive('getPaths')
            ->once()
            ->with('default')
            ->andReturn([
                'config.json' => 'config.json',
                'static/extra' => '',
            ]);

        $assetFactory = Mockery::mock(AssetFactoryInterface::class);

        $assetFactory->shouldReceive('create')
            ->once()
            ->with($filesystem, 'config.json', 'config.json')
            ->andReturn([
                new Asset('config.json', 'config.json'),
            ]);

        $assetFactory->shouldReceive('create')
            ->once()
            ->with($filesystem, 'static/extra', '')
            ->andReturn([
                new Asset('static/extra/a.json', 'a.json'),
                new Asset('static/extra/b.json', 'b.json'),
            ]);

        $fileStorage = Mockery::mock(FileStorageInterface::class);

        $fileStorage->shouldReceive('getName')
            ->andReturn('default');

        foreach ([['config.json', 'config.json'], ['static/extra/a.json', 'a.json'], ['static/extra/b.json', 'b.json']] as [$from, $to]) {
            $pathInfo = Mockery::mock(PathInfoInterface::class);
            $resource = new SimpleResource($pathInfo, '{}');

            $fileStorage->shouldReceive('createPathInfo')
                ->once()
                ->with($to)
                ->andReturn($pathInfo);

            $fileStorage->shouldReceive('createResourceFromLocalFile')
                ->once()
                ->with($pathInfo, $from)
                ->andReturn($resource);

            $fileStorage->shouldReceive('save')
                ->once()
                ->with($resource)
                ->andReturns();
        }

        $assetsCopier = new AssetsCopier($filesystem, $pathsProvider, $assetFactory);
        $logger = new TestLogger();

        $assetsCopier->copy($fileStorage, $logger);

        Assert::same([
            [
                'level' => 'info',
                'message' => 'Copying config.json to default://config.json',
                'context' => [],
            ],
            [
                'level' => 'info',
                'message' => 'Copying static/extra/a.json to default://a.json',
                'context' => [],
            ],
            [
                'level' => 'info',
                'message' => 'Copying static/extra/b.json to default://b.json',
                'context' => [],
            ],
        ], $logger->records);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

(new AssetCopierTest())->run();
