<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Fixtures\NetteDI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use SixtyEightPublishers\FileStorage\Bridge\Nette\DI\Config\StorageConfig;
use SixtyEightPublishers\FileStorage\Bridge\Nette\DI\FileStorageDefinitionFactoryInterface;
use SixtyEightPublishers\FileStorage\Bridge\Nette\DI\FileStorageExtension;
use SixtyEightPublishers\FileStorage\Tests\Fixtures\CustomFileStorage;
use function array_values;
use function assert;

final class CustomFileStorageDefinitionFactoryExtension extends CompilerExtension implements FileStorageDefinitionFactoryInterface
{
    public function canCreateFileStorage(string $name, StorageConfig $config): bool
    {
        return 'custom' === $name;
    }

    public function createFileStorage(string $name, StorageConfig $config): ServiceDefinition
    {
        $ext = array_values($this->compiler->getExtensions(FileStorageExtension::class))[0];
        assert($ext instanceof FileStorageExtension);

        $fileStorage = $ext->createFileStorage($name, $config);
        $fileStorage->setFactory(CustomFileStorage::class, $fileStorage->getFactory()->arguments);

        return $fileStorage;
    }
}
