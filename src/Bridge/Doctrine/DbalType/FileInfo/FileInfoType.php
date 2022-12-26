<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Doctrine\DbalType\FileInfo;

use Doctrine\DBAL\Types\JsonType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use SixtyEightPublishers\FileStorage\FileInfoInterface;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use SixtyEightPublishers\FileStorage\Exception\RuntimeException;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;

class FileInfoType extends JsonType
{
	public const NAME = 'file_info';

	private ?FileStorageProviderInterface $fileStorageProvider = null;

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
				$value = $fileStorage->createPathInfo((string) $value);
			}

			$value = $fileStorage->createFileInfo($value);
		}

		return parent::convertToDatabaseValue($value, $platform);
	}

	public function convertToPHPValue($value, AbstractPlatform $platform): ?FileInfoInterface
	{
		$value = parent::convertToPHPValue($value, $platform);

		if (null === $value) {
			return null;
		}

		$fileStorage = $this->getFileStorageProvider()->get($value['storage'] ?? null);
		$pathInfo = $fileStorage->createPathInfo($value['path'] ?? '');

		if (isset($value['version'])) {
			$pathInfo->setVersion($value['version']);
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
				static::class
			));
		}

		return $this->fileStorageProvider;
	}
}
