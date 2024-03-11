<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Doctrine\DbalType;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use Nette\DI\Container;
use SixtyEightPublishers\DoctrineBridge\Type\ContainerAwareTypeInterface;
use SixtyEightPublishers\FileStorage\Exception\RuntimeException;
use SixtyEightPublishers\FileStorage\FileInfoInterface;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use function assert;
use function is_array;
use function is_string;

class FileInfoType extends JsonType implements ContainerAwareTypeInterface
{
    public const NAME = 'file_info';

    private ?FileStorageProviderInterface $fileStorageProvider = null;

    public function setContainer(Container $container, array $context = []): void
    {
        $this->setFileStorageProvider(
            $container->getByType(FileStorageProviderInterface::class),
        );
    }

    public function setFileStorageProvider(FileStorageProviderInterface $fileStorageProvider): void
    {
        $this->fileStorageProvider = $fileStorageProvider;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof FileInfoInterface) {
            $fileStorage = $this->getFileStorageProvider()->get();

            if (!$value instanceof PathInfoInterface) {
                assert(is_string($value));
                $value = $fileStorage->createPathInfo($value);
            }

            $value = $fileStorage->createFileInfo($value);
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?FileInfoInterface
    {
        $value = parent::convertToPHPValue($value, $platform);
        assert(null === $value || is_array($value));

        if (null === $value) {
            return null;
        }

        $fileStorage = $this->getFileStorageProvider()->get($value['storage'] ?? null);
        $pathInfo = $fileStorage->createPathInfo($value['path'] ?? '');

        if (isset($value['version'])) {
            $pathInfo = $pathInfo->withVersion($value['version']);
        }

        return $fileStorage->createFileInfo($pathInfo);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    private function getFileStorageProvider(): FileStorageProviderInterface
    {
        if (null === $this->fileStorageProvider) {
            throw new RuntimeException(sprintf(
                'Please call method %s::setFileStorageProvider().',
                static::class,
            ));
        }

        return $this->fileStorageProvider;
    }
}
