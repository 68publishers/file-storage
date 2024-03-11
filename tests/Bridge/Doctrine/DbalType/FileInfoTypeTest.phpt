<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Bridge\Doctrine\DbalType;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Mockery;
use SixtyEightPublishers\FileStorage\Bridge\Doctrine\DbalType\FileInfoType;
use SixtyEightPublishers\FileStorage\Exception\RuntimeException;
use SixtyEightPublishers\FileStorage\FileInfoInterface;
use SixtyEightPublishers\FileStorage\FileStorageInterface;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../../bootstrap.php';

final class FileInfoTypeTest extends TestCase
{
    public function testNameShouldBeReturned(): void
    {
        $type = new FileInfoType();

        Assert::same(FileInfoType::NAME, $type->getName());
    }

    public function testExceptionShouldBeThrownIfFileStorageProviderNotSet(): void
    {
        $type = new FileInfoType();
        $platform = Mockery::mock(AbstractPlatform::class);

        Assert::exception(
            static fn () => $type->convertToDatabaseValue('static/file.json', $platform),
            RuntimeException::class,
            'Please call method SixtyEightPublishers\FileStorage\Bridge\Doctrine\DbalType\FileInfoType::setFileStorageProvider().',
        );

        Assert::exception(
            static fn () => $type->convertToPHPValue('{}', $platform),
            RuntimeException::class,
            'Please call method SixtyEightPublishers\FileStorage\Bridge\Doctrine\DbalType\FileInfoType::setFileStorageProvider().',
        );
    }

    public function testConvertNullToDatabaseValue(): void
    {
        $type = new FileInfoType();
        $platform = Mockery::mock(AbstractPlatform::class);
        $fileStorageProvider = Mockery::mock(FileStorageProviderInterface::class);

        $type->setFileStorageProvider($fileStorageProvider);

        Assert::null($type->convertToDatabaseValue(null, $platform));
    }

    public function testConvertFileInfoToDatabaseValue(): void
    {
        $type = new FileInfoType();
        $platform = Mockery::mock(AbstractPlatform::class);
        $fileStorageProvider = Mockery::mock(FileStorageProviderInterface::class);
        $value = Mockery::mock(FileInfoInterface::class);

        $value->shouldReceive('jsonSerialize')
            ->once()
            ->andReturn([
                'path' => 'static/file.json',
                'storage' => 'default',
                'version' => null,
            ]);

        $type->setFileStorageProvider($fileStorageProvider);

        Assert::same('{"path":"static\/file.json","storage":"default","version":null}', $type->convertToDatabaseValue($value, $platform));
    }

    public function testConvertPathInfoToDatabaseValue(): void
    {
        $type = new FileInfoType();
        $platform = Mockery::mock(AbstractPlatform::class);
        $fileStorageProvider = Mockery::mock(FileStorageProviderInterface::class);
        $fileStorage = Mockery::mock(FileStorageInterface::class);
        $fileInfo = Mockery::mock(FileInfoInterface::class);
        $value = Mockery::mock(PathInfoInterface::class);

        $fileStorageProvider->shouldReceive('get')
            ->once()
            ->withNoArgs()
            ->andReturn($fileStorage);

        $fileStorage->shouldReceive('createFileInfo')
            ->once()
            ->with($value)
            ->andReturn($fileInfo);

        $fileInfo->shouldReceive('jsonSerialize')
            ->once()
            ->andReturn([
                'path' => 'static/file.json',
                'storage' => 'default',
                'version' => null,
            ]);

        $type->setFileStorageProvider($fileStorageProvider);

        Assert::same('{"path":"static\/file.json","storage":"default","version":null}', $type->convertToDatabaseValue($value, $platform));
    }

    public function testConvertStringToDatabaseValue(): void
    {
        $type = new FileInfoType();
        $platform = Mockery::mock(AbstractPlatform::class);
        $fileStorageProvider = Mockery::mock(FileStorageProviderInterface::class);
        $fileStorage = Mockery::mock(FileStorageInterface::class);
        $pathInfo = Mockery::mock(PathInfoInterface::class);
        $fileInfo = Mockery::mock(FileInfoInterface::class);
        $value = 'static/file.json';

        $fileStorageProvider->shouldReceive('get')
            ->once()
            ->withNoArgs()
            ->andReturn($fileStorage);

        $fileStorage->shouldReceive('createPathInfo')
            ->once()
            ->with($value)
            ->andReturn($pathInfo);

        $fileStorage->shouldReceive('createFileInfo')
            ->once()
            ->with($pathInfo)
            ->andReturn($fileInfo);

        $fileInfo->shouldReceive('jsonSerialize')
            ->once()
            ->andReturn([
                'path' => 'static/file.json',
                'storage' => 'default',
                'version' => null,
            ]);

        $type->setFileStorageProvider($fileStorageProvider);

        Assert::same('{"path":"static\/file.json","storage":"default","version":null}', $type->convertToDatabaseValue($value, $platform));
    }

    public function testConvertNullToPhpValue(): void
    {
        $type = new FileInfoType();
        $platform = Mockery::mock(AbstractPlatform::class);
        $fileStorageProvider = Mockery::mock(FileStorageProviderInterface::class);

        $type->setFileStorageProvider($fileStorageProvider);

        Assert::null($type->convertToPHPValue(null, $platform));
    }

    public function testConvertJsonToPhpValue(): void
    {
        $type = new FileInfoType();
        $platform = Mockery::mock(AbstractPlatform::class);
        $fileStorageProvider = Mockery::mock(FileStorageProviderInterface::class);
        $fileStorage = Mockery::mock(FileStorageInterface::class);
        $pathInfo = Mockery::mock(PathInfoInterface::class);
        $fileInfo = Mockery::mock(FileInfoInterface::class);

        $fileStorageProvider->shouldReceive('get')
            ->once()
            ->with('default')
            ->andReturn($fileStorage);

        $fileStorage->shouldReceive('createPathInfo')
            ->once()
            ->with('static/file.json')
            ->andReturn($pathInfo);

        $fileStorage->shouldReceive('createFileInfo')
            ->once()
            ->with($pathInfo)
            ->andReturn($fileInfo);

        $type->setFileStorageProvider($fileStorageProvider);

        Assert::same($fileInfo, $type->convertToPHPValue('{"path":"static\/file.json","storage":"default","version":null}', $platform));
    }

    public function testConvertJsonToPhpValueWithVersion(): void
    {
        $type = new FileInfoType();
        $platform = Mockery::mock(AbstractPlatform::class);
        $fileStorageProvider = Mockery::mock(FileStorageProviderInterface::class);
        $fileStorage = Mockery::mock(FileStorageInterface::class);
        $pathInfo = Mockery::mock(PathInfoInterface::class);
        $versionedPathInfo = Mockery::mock(PathInfoInterface::class);
        $fileInfo = Mockery::mock(FileInfoInterface::class);

        $fileStorageProvider->shouldReceive('get')
            ->once()
            ->with('default')
            ->andReturn($fileStorage);

        $fileStorage->shouldReceive('createPathInfo')
            ->once()
            ->with('static/file.json')
            ->andReturn($pathInfo);

        $fileStorage->shouldReceive('createFileInfo')
            ->once()
            ->with($versionedPathInfo)
            ->andReturn($fileInfo);

        $pathInfo->shouldReceive('withVersion')
            ->once()
            ->with('123')
            ->andReturn($versionedPathInfo);

        $type->setFileStorageProvider($fileStorageProvider);

        Assert::same($fileInfo, $type->convertToPHPValue('{"path":"static\/file.json","storage":"default","version":"123"}', $platform));
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

(new FileInfoTypeTest())->run();
