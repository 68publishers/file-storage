<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Bridge\Nette\DI;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use SixtyEightPublishers\FileStorage\Bridge\Doctrine\DbalType\FileInfoType;
use SixtyEightPublishers\FileStorage\Exception\RuntimeException;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class FileStorageDoctrineExtensionTest extends TestCase
{
    public function testExceptionShouldBeThrownIfDoctrineBridgeExtensionNotRegistered(): void
    {
        Assert::exception(
            static fn () => ContainerFactory::create(__DIR__ . '/config/FileStorageDoctrine/config.error.missingDoctrineBridgeExtension.neon'),
            RuntimeException::class,
            "The extension SixtyEightPublishers\FileStorage\Bridge\Nette\DI\FileStorageDoctrineExtension can be used only with SixtyEightPublishers\DoctrineBridge\Bridge\Nette\DI\DoctrineBridgeExtension.",
        );
    }

    public function testExceptionShouldBeThrownIfFileStorageExtensionNotRegistered(): void
    {
        Assert::exception(
            static fn () => ContainerFactory::create(__DIR__ . '/config/FileStorageDoctrine/config.error.missingFileStorageExtension.neon'),
            RuntimeException::class,
            "The extension SixtyEightPublishers\FileStorage\Bridge\Nette\DI\FileStorageDoctrineExtension can be used only with SixtyEightPublishers\FileStorage\Bridge\Nette\DI\FileStorageExtension.",
        );
    }

    public function testExtensionShouldBeIntegrated(): void
    {
        $container = ContainerFactory::create(__DIR__ . '/config/FileStorageDoctrine/config.neon');
        $container->getByType(Connection::class); # create connection with types

        Assert::true(Type::hasType(FileInfoType::NAME));

        $type = Type::getType(FileInfoType::NAME);

        Assert::type(FileInfoType::class, $type);
    }

    public function testExtensionShouldBeIntegratedWithCustomTypeName(): void
    {
        $container = ContainerFactory::create(__DIR__ . '/config/FileStorageDoctrine/config.withCustomTypeName.neon');
        $container->getByType(Connection::class); # create connection with types

        Assert::true(Type::hasType('custom_file_info'));

        $type = Type::getType('custom_file_info');

        Assert::type(FileInfoType::class, $type);
    }
}

(new FileStorageDoctrineExtensionTest())->run();
