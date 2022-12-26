<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage;

use ArrayIterator;
use SixtyEightPublishers\FileStorage\Exception\InvalidArgumentException;
use function sprintf;

final class FileStorageProvider implements FileStorageProviderInterface
{
	/** @var array<string, FileStorageInterface> */
	private array $fileStorageInstances = [];

	/**
	 * @param array<FileStorageInterface> $fileStorageInstances
	 */
	public function __construct(
		private readonly FileStorageInterface $defaultFileStorage,
		array $fileStorageInstances,
	) {
		$this->addFileStorage($defaultFileStorage);

		foreach ($fileStorageInstances as $fileStorageInstance) {
			$this->addFileStorage($fileStorageInstance);
		}
	}

	public function get(?string $name = null): FileStorageInterface
	{
		if (null === $name) {
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
	 * @return ArrayIterator<string, FileStorageInterface>
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->fileStorageInstances);
	}

	private function addFileStorage(FileStorageInterface $fileStorage): void
	{
		$this->fileStorageInstances[$fileStorage->getName()] = $fileStorage;
	}
}
