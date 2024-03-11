<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Asset;

use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use SixtyEightPublishers\FileStorage\Asset\Asset;
use SixtyEightPublishers\FileStorage\Asset\AssetFactory;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

final class AssetFactoryTest extends TestCase
{
    public function testFileAssetShouldBeCreated(): void
    {
        $filesystem = $this->createFilesystem([
            'static/config.json' => '{}',
            'static/extra/a.json' => '{}',
        ]);

        $factory = new AssetFactory();
        $assets = $factory->create($filesystem, 'static/config.json', 'destination/config.json');

        Assert::equal([
            new Asset('static/config.json', 'destination/config.json'),
        ], $assets);
    }

    public function testAssetsShouldBeEmptyOnMissingPath(): void
    {
        $filesystem = $this->createFilesystem([
            'static/config.json' => '{}',
            'static/extra/a.json' => '{}',
        ]);

        $factory = new AssetFactory();
        $assets = $factory->create($filesystem, 'missing', 'destination/missing');

        Assert::equal([], $assets);
    }

    public function testAssetsFromDirectoryShouldBeCreated(): void
    {
        $filesystem = $this->createFilesystem([
            'static/config.json' => '{}',
            'static/extra/a.json' => '{}',
            'static/extra/b.json' => '{}',
            'static/extra/builds/entrypoints.json' => '{}',
        ]);

        $factory = new AssetFactory();
        $assets = $factory->create($filesystem, 'static/extra', 'extra');

        Assert::equal([
            new Asset('static/extra/a.json', 'extra/a.json'),
            new Asset('static/extra/b.json', 'extra/b.json'),
            new Asset('static/extra/builds/entrypoints.json', 'extra/builds/entrypoints.json'),
        ], $assets);
    }

    public function testAssetsFromDirectoryShouldBeCreatedWithEmptyDestination(): void
    {
        $filesystem = $this->createFilesystem([
            'static/config.json' => '{}',
            'static/extra/a.json' => '{}',
            'static/extra/b.json' => '{}',
            'static/extra/builds/entrypoints.json' => '{}',
        ]);

        $factory = new AssetFactory();
        $assets = $factory->create($filesystem, 'static/extra', '');

        Assert::equal([
            new Asset('static/extra/a.json', 'a.json'),
            new Asset('static/extra/b.json', 'b.json'),
            new Asset('static/extra/builds/entrypoints.json', 'builds/entrypoints.json'),
        ], $assets);
    }

    private function createFilesystem(array $files): Filesystem
    {
        $fs = new Filesystem(
            new InMemoryFilesystemAdapter(),
        );

        foreach ($files as $filename => $content) {
            $fs->write($filename, $content);
        }

        return $fs;
    }
}

(new AssetFactoryTest())->run();
