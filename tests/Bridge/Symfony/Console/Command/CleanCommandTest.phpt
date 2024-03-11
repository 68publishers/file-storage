<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Bridge\Symfony\Console\Command;

use ArrayIterator;
use League\Flysystem\FilesystemOperator;
use Mockery;
use SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Command\CleanCommand;
use SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Configurator\BaseCleanCommandConfigurator;
use SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Configurator\CleanCommandConfiguratorRegistry;
use SixtyEightPublishers\FileStorage\Cleaner\StorageCleanerInterface;
use SixtyEightPublishers\FileStorage\FileStorageInterface;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tester\Assert;
use Tester\TestCase;
use function assert;

require __DIR__ . '/../../../../bootstrap.php';

final class CleanCommandTest extends TestCase
{
    public function testAllStoragesShouldBeCleaned(): void
    {
        $provider = Mockery::mock(FileStorageProviderInterface::class);
        $storage = Mockery::mock(FileStorageInterface::class);
        $filesystem = Mockery::mock(FilesystemOperator::class);
        $cleaner = Mockery::mock(StorageCleanerInterface::class);

        $provider->shouldReceive('getIterator')
            ->once()
            ->andReturn(new ArrayIterator([
                'default' => $storage,
            ]));

        $storage->shouldReceive('getFilesystem')
            ->times(2)
            ->andReturn($filesystem);

        $storage->shouldReceive('getName')
            ->once()
            ->andReturn('default');

        $cleaner->shouldReceive('getCount')
            ->once()
            ->with($filesystem, [])
            ->andReturn(10);

        $cleaner->shouldReceive('clean')
            ->once()
            ->with($filesystem, []);

        $tester = $this->createCommandTester($provider, $cleaner);

        $tester->setInputs(['yes']);
        $tester->execute([]);

        $display = $tester->getDisplay();

        Assert::same(0, $tester->getStatusCode());
        Assert::contains('Do you want to delete 10 files in a storage "default"?', $display);
        Assert::contains('Storage "default" has been successfully cleaned.', $display);
    }

    public function testAllStoragesShouldBeCleanedWithNamespaceOption(): void
    {
        $provider = Mockery::mock(FileStorageProviderInterface::class);
        $storage = Mockery::mock(FileStorageInterface::class);
        $filesystem = Mockery::mock(FilesystemOperator::class);
        $cleaner = Mockery::mock(StorageCleanerInterface::class);

        $provider->shouldReceive('getIterator')
            ->once()
            ->andReturn(new ArrayIterator([
                'default' => $storage,
            ]));

        $storage->shouldReceive('getFilesystem')
            ->times(2)
            ->andReturn($filesystem);

        $storage->shouldReceive('getName')
            ->once()
            ->andReturn('default');

        $cleaner->shouldReceive('getCount')
            ->once()
            ->with($filesystem, [
                StorageCleanerInterface::OPTION_NAMESPACE => 'static',
            ])
            ->andReturn(10);

        $cleaner->shouldReceive('clean')
            ->once()
            ->with($filesystem, [
                StorageCleanerInterface::OPTION_NAMESPACE => 'static',
            ]);

        $tester = $this->createCommandTester($provider, $cleaner);

        $tester->setInputs(['yes']);
        $tester->execute([
            '--namespace' => 'static',
        ]);

        $display = $tester->getDisplay();

        Assert::same(0, $tester->getStatusCode());
        Assert::contains('Do you want to delete 10 files in a storage "default"?', $display);
        Assert::contains('Storage "default" has been successfully cleaned.', $display);
    }

    public function testAllStoragesShouldNotBeCleanedBecauseOfNegativeAnswer(): void
    {
        $provider = Mockery::mock(FileStorageProviderInterface::class);
        $storage = Mockery::mock(FileStorageInterface::class);
        $filesystem = Mockery::mock(FilesystemOperator::class);
        $cleaner = Mockery::mock(StorageCleanerInterface::class);

        $provider->shouldReceive('getIterator')
            ->once()
            ->andReturn(new ArrayIterator([
                'default' => $storage,
            ]));

        $storage->shouldReceive('getFilesystem')
            ->once()
            ->andReturn($filesystem);

        $cleaner->shouldReceive('getCount')
            ->once()
            ->with($filesystem, [])
            ->andReturn(10);

        $tester = $this->createCommandTester($provider, $cleaner);

        $tester->setInputs(['no']);
        $tester->execute([]);

        $display = $tester->getDisplay();

        Assert::same(0, $tester->getStatusCode());
        Assert::contains('Do you want to delete 10 files in a storage "default"?', $display);
        Assert::notContains('Storage "default" has been successfully cleaned.', $display);
    }

    public function testSpecifiedStorageShouldBeCleaned(): void
    {
        $provider = Mockery::mock(FileStorageProviderInterface::class);
        $storage = Mockery::mock(FileStorageInterface::class);
        $filesystem = Mockery::mock(FilesystemOperator::class);
        $cleaner = Mockery::mock(StorageCleanerInterface::class);

        $provider->shouldReceive('get')
            ->once()
            ->with('default')
            ->andReturn($storage);

        $storage->shouldReceive('getFilesystem')
            ->once()
            ->andReturn($filesystem);

        $cleaner->shouldReceive('getCount')
            ->once()
            ->with($filesystem, [])
            ->andReturn(15);

        $cleaner->shouldReceive('clean')
            ->once()
            ->with($filesystem, []);

        $tester = $this->createCommandTester($provider, $cleaner);

        $tester->setInputs(['yes']);
        $tester->execute([
            'storage' => 'default',
        ]);

        $display = $tester->getDisplay();

        Assert::same(0, $tester->getStatusCode());
        Assert::contains('Do you want to delete 15 files in a storage "default"?', $display);
        Assert::contains('Storage "default" has been successfully cleaned.', $display);
    }

    public function testSpecifiedStorageShouldBeCleanedWithNamespaceOption(): void
    {
        $provider = Mockery::mock(FileStorageProviderInterface::class);
        $storage = Mockery::mock(FileStorageInterface::class);
        $filesystem = Mockery::mock(FilesystemOperator::class);
        $cleaner = Mockery::mock(StorageCleanerInterface::class);

        $provider->shouldReceive('get')
            ->once()
            ->with('default')
            ->andReturn($storage);

        $storage->shouldReceive('getFilesystem')
            ->once()
            ->andReturn($filesystem);

        $cleaner->shouldReceive('getCount')
            ->once()
            ->with($filesystem, [
                StorageCleanerInterface::OPTION_NAMESPACE => 'static',
            ])
            ->andReturn(15);

        $cleaner->shouldReceive('clean')
            ->once()
            ->with($filesystem, [
                StorageCleanerInterface::OPTION_NAMESPACE => 'static',
            ]);

        $tester = $this->createCommandTester($provider, $cleaner);

        $tester->setInputs(['yes']);
        $tester->execute([
            'storage' => 'default',
            '--namespace' => 'static',
        ]);

        $display = $tester->getDisplay();

        Assert::same(0, $tester->getStatusCode());
        Assert::contains('Do you want to delete 15 files in a storage "default"?', $display);
        Assert::contains('Storage "default" has been successfully cleaned.', $display);
    }

    public function testSpecifiedStorageShouldNotBeCleanedBecauseOfNegativeAnswer(): void
    {
        $provider = Mockery::mock(FileStorageProviderInterface::class);
        $storage = Mockery::mock(FileStorageInterface::class);
        $filesystem = Mockery::mock(FilesystemOperator::class);
        $cleaner = Mockery::mock(StorageCleanerInterface::class);

        $provider->shouldReceive('get')
            ->once()
            ->with('default')
            ->andReturn($storage);

        $storage->shouldReceive('getFilesystem')
            ->once()
            ->andReturn($filesystem);

        $cleaner->shouldReceive('getCount')
            ->once()
            ->with($filesystem, [])
            ->andReturn(15);

        $tester = $this->createCommandTester($provider, $cleaner);

        $tester->setInputs(['no']);
        $tester->execute([
            'storage' => 'default',
        ]);

        $display = $tester->getDisplay();

        Assert::same(0, $tester->getStatusCode());
        Assert::contains('Do you want to delete 15 files in a storage "default"?', $display);
        Assert::notContains('Storage "default" has been successfully cleaned.', $display);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    private function createCommandTester(FileStorageProviderInterface $fileStorageProvider, StorageCleanerInterface $storageCleaner): CommandTester
    {
        $configuration = new CleanCommandConfiguratorRegistry([
            new BaseCleanCommandConfigurator(),
        ]);

        $command = new CleanCommand($fileStorageProvider, $storageCleaner, $configuration);
        $application = new Application();

        $application->add($command);

        $command = $application->find('file-storage:clean');
        assert($command instanceof CleanCommand);

        return new CommandTester($command);
    }
}

(new CleanCommandTest())->run();
