<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage;

use ArrayIterator;
use SixtyEightPublishers\FileStorage\Exception\InvalidArgumentException;

final class FileStorageProvider implements FileStorageProviderInterface
{
	/** @var \SixtyEightPublishers\FileStorage\FileStorageInterface  */
	private $defaultFileStorage;

	/** @var \SixtyEightPublishers\FileStorage\FileStorageInterface[]  */
	private $fileStorageInstances = [];

	/**
	 * @param \SixtyEightPublishers\FileStorage\FileStorageInterface   $defaultFileStorage
	 * @param \SixtyEightPublishers\FileStorage\FileStorageInterface[] $fileStorageInstances
	 */
	public function __construct(FileStorageInterface $defaultFileStorage, array $fileStorageInstances)
	{
		$this->defaultFileStorage = $defaultFileStorage;

		$this->addFileStorage($defaultFileStorage);

		foreach ($fileStorageInstances as $fileStorageInstance) {
			$this->addFileStorage($fileStorageInstance);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function get(?string $name = NULL): FileStorageInterface
	{
		if (NULL === $name) {
			return $this->defaultFileStorage;
		}

		if (!isset($this->fileStorageInstances[$name])) {
			throw new InvalidArgumentException(sprintf(
				'FileStorage with name "%s" is not defined.',
				$name
			));
		}

		return $this->fileStorageInstances[$name];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->fileStorageInstances);
	}

	/**
	 * @param \SixtyEightPublishers\FileStorage\FileStorageInterface $fileStorage
	 *
	 * @return void
	 */
	private function addFileStorage(FileStorageInterface $fileStorage): void
	{
		$this->fileStorageInstances[$fileStorage->getName()] = $fileStorage;
	}
}
