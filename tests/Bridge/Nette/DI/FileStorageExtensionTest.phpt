<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Bridge\Nette\DI;

use Closure;
use League\Flysystem\Config as FlysystemConfig;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\Visibility;
use Nette\DI\Container;
use Nette\DI\InvalidConfigurationException;
use SixtyEightPublishers\FileStorage\Asset\AssetsCopier;
use SixtyEightPublishers\FileStorage\Asset\AssetsCopierInterface;
use SixtyEightPublishers\FileStorage\Asset\PathsProvider;
use SixtyEightPublishers\FileStorage\Cleaner\DefaultFileKeepResolver;
use SixtyEightPublishers\FileStorage\Cleaner\StorageCleaner;
use SixtyEightPublishers\FileStorage\Cleaner\StorageCleanerInterface;
use SixtyEightPublishers\FileStorage\FileStorage;
use SixtyEightPublishers\FileStorage\FileStorageInterface;
use SixtyEightPublishers\FileStorage\FileStorageProvider;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;
use SixtyEightPublishers\FileStorage\Tests\Fixtures\CustomFileStorage;
use Tester\Assert;
use Tester\CodeCoverage\Collector;
use Tester\TestCase;
use function assert;
use function call_user_func;
use function get_class;

require __DIR__ . '/../../../bootstrap.php';

final class FileStorageExtensionTest extends TestCase
{
    public function testExceptionShouldBeThrownIfNoStorageRegistered(): void
    {
        Assert::exception(
            static fn () => ContainerFactory::create(__DIR__ . '/config/FileStorage/config.error.noStorage.neon'),
            InvalidConfigurationException::class,
            '%a?%At least one storage must be defined.%a?%',
        );
    }

    public function testExtensionShouldBeIntegrated(): void
    {
        $container = ContainerFactory::create(__DIR__ . '/config/FileStorage/config.neon');

        $this->assertFileStorages($container);
        $this->assertStorageCleaner($container);
        $this->assertAssets($container, [
            'default' => [
                'assets/config.json' => 'copied/config.json',
                'assets/images' => 'copied/images',
            ],
            'other' => [
                'assets/config.json' => 'copied/conf/config.test.json',
                'assets/images' => 'copied',
            ],
        ]);
    }

    public function testAssetsShouldBeProvidedViaExtension(): void
    {
        $container = ContainerFactory::create(__DIR__ . '/config/FileStorage/config.withAssetsProviderExtension.neon');

        $this->assertAssets($container, [
            'default' => [
                'assets/config.json' => 'copied/config.json',
                'assets/images' => 'copied/images',
                'test' => 'test',
            ],
            'other' => [
                'assets/config.json' => 'copied/conf/config.test.json',
                'assets/images' => 'copied',
            ],
        ]);
    }

    public function testCustomFileStorageDefinitionFactoryShouldBeUsed(): void
    {
        $container = ContainerFactory::create(__DIR__ . '/config/FileStorage/config.withCustomFileStorageDefinitionFactoryExtension.neon');
        $provider = $container->getByType(FileStorageProviderInterface::class);

        assert($provider instanceof FileStorageProvider);

        Assert::same(FileStorage::class, get_class($provider->get('default')));
        Assert::same(FileStorage::class, get_class($provider->get('other')));
        Assert::same(CustomFileStorage::class, get_class($provider->get('custom')));
    }

    protected function tearDown(): void
    {
        # save manually partial code coverage to free memory
        if (Collector::isStarted()) {
            Collector::save();
        }
    }

    private function assertFileStorages(Container $container): void
    {
        $provider = $container->getByType(FileStorageProviderInterface::class);

        Assert::type(FileStorageProvider::class, $provider);
        assert($provider instanceof FileStorageProvider);

        Assert::noError(
            static function () use ($provider): void {
                $provider->get();
                $provider->get('default');
                $provider->get('other');
            },
        );

        Assert::same($provider->get(), $container->getByType(FileStorageInterface::class));

        $defaultFilesystem = $provider->get('default')->getFilesystem();
        $otherFilesystem = $provider->get('other')->getFilesystem();
        assert($defaultFilesystem instanceof Filesystem && $otherFilesystem instanceof Filesystem);

        $options = [
            FlysystemConfig::OPTION_VISIBILITY => Visibility::PUBLIC,
            FlysystemConfig::OPTION_DIRECTORY_VISIBILITY => Visibility::PUBLIC,
        ];

        $this->assertInMemoryFilesystem($defaultFilesystem, $options);
        $this->assertInMemoryFilesystem($otherFilesystem, $options);
    }

    private function assertLocalFilesystem(Filesystem $filesystem, array $configOptions, string $rootLocation): void
    {
        call_user_func(Closure::bind(
            static function () use ($filesystem, $configOptions, $rootLocation): void {
                Assert::type(LocalFilesystemAdapter::class, $filesystem->adapter);

                foreach ($configOptions as $opt => $value) {
                    Assert::same($value, $filesystem->config->get($opt));
                }

                $adapter = $filesystem->adapter;
                assert($adapter instanceof LocalFilesystemAdapter);

                call_user_func(Closure::bind(
                    static function () use ($adapter, $rootLocation): void {
                        Assert::same($rootLocation, $adapter->rootLocation);
                    },
                    null,
                    LocalFilesystemAdapter::class,
                ));
            },
            null,
            Filesystem::class,
        ));
    }

    private function assertInMemoryFilesystem(Filesystem $filesystem, array $configOptions): void
    {
        call_user_func(Closure::bind(
            static function () use ($filesystem, $configOptions): void {
                Assert::type(InMemoryFilesystemAdapter::class, $filesystem->adapter);

                foreach ($configOptions as $opt => $value) {
                    Assert::same($value, $filesystem->config->get($opt));
                }
            },
            null,
            Filesystem::class,
        ));
    }

    private function assertStorageCleaner(Container $container): void
    {
        $cleaner = $container->getByType(StorageCleanerInterface::class);

        Assert::type(StorageCleaner::class, $cleaner);
        assert($cleaner instanceof StorageCleaner);

        call_user_func(Closure::bind(
            static function () use ($cleaner): void {
                Assert::type(DefaultFileKeepResolver::class, $cleaner->fileKeepResolver);
            },
            null,
            $cleaner,
        ));
    }

    private function assertAssets(Container $container, array $paths): void
    {
        $copier = $container->getByType(AssetsCopierInterface::class);

        Assert::type(AssetsCopier::class, $copier);
        assert($copier instanceof AssetsCopier);

        $assertFs = function (FilesystemOperator $filesystem, string $rootLocation): void {
            Assert::type(Filesystem::class, $filesystem);
            assert($filesystem instanceof Filesystem);

            $this->assertLocalFilesystem($filesystem, [], $rootLocation);
        };

        call_user_func(Closure::bind(
            static function () use ($copier, $assertFs, $paths): void {
                $assertFs($copier->localFilesystemOperator, __DIR__);

                $pathsProvider = $copier->pathsProvider;

                call_user_func(Closure::bind(
                    static function () use ($pathsProvider, $paths): void {
                        Assert::same($paths, $pathsProvider->pathsMap);
                    },
                    null,
                    PathsProvider::class,
                ));
            },
            null,
            $copier,
        ));
    }
}

(new FileStorageExtensionTest())->run();
