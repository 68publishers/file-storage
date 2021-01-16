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

	/** @var \SixtyEightPublishers\FileStorage\FileStorageProviderInterface|NULL */
	private $fileStorageProvider;

	/**
	 * @param \SixtyEightPublishers\FileStorage\FileStorageProviderInterface $fileStorageProvider
	 *
	 * @return void
	 */
	public function setFileStorageProvider(FileStorageProviderInterface $fileStorageProvider): void
	{
		$this->fileStorageProvider = $fileStorageProvider;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws \Doctrine\DBAL\Types\ConversionException
	 */
	public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
	{
		if (NULL === $value) {
			return NULL;
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

	/**
	 * {@inheritdoc}
	 *
	 * @throws \Doctrine\DBAL\Types\ConversionException
	 */
	public function convertToPHPValue($value, AbstractPlatform $platform): ?FileInfoInterface
	{
		$value = parent::convertToPHPValue($value, $platform);

		if (NULL === $value) {
			return NULL;
		}

		$fileStorage = $this->getFileStorageProvider()->get($value['storage'] ?? NULL);
		$pathInfo = $fileStorage->createPathInfo($value['path'] ?? '');

		if (isset($value['version'])) {
			$pathInfo->setVersion($value['version']);
		}

		return $fileStorage->createFileInfo($pathInfo);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName(): string
	{
		return self::NAME;
	}

	/**
	 * {@inheritdoc}
	 */
	public function requiresSQLCommentHint(AbstractPlatform $platform): bool
	{
		return TRUE;
	}

	/**
	 * @return \SixtyEightPublishers\FileStorage\FileStorageProviderInterface
	 */
	private function getFileStorageProvider(): FileStorageProviderInterface
	{
		if (NULL === $this->fileStorageProvider) {
			throw new RuntimeException(sprintf(
				'Please call method %s::setFileStorageProvider().',
				$this->fileStorageProvider
			));
		}

		return $this->fileStorageProvider;
	}
}
