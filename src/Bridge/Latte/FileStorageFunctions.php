<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Latte;

use Latte\Engine;
use SixtyEightPublishers\FileStorage\FileInfoInterface;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;

final class FileStorageFunctions
{
	public const FUNCTION_ID_CREATE_FILE_INFO = 'create_file_info';

	public const DEFAULT_FUNCTION_NAMES = [
		self::FUNCTION_ID_CREATE_FILE_INFO => 'file_info',
	];

	private const FUNCTION_CALLBACKS = [
		self::FUNCTION_ID_CREATE_FILE_INFO => 'createFileInfo',
	];

	/** @var \SixtyEightPublishers\FileStorage\FileStorageProviderInterface  */
	private $fileStorageProvider;

	/**
	 * @param \SixtyEightPublishers\FileStorage\FileStorageProviderInterface $fileStorageProvider
	 */
	public function __construct(FileStorageProviderInterface $fileStorageProvider)
	{
		$this->fileStorageProvider = $fileStorageProvider;
	}

	/**
	 * @param \SixtyEightPublishers\FileStorage\FileStorageProviderInterface $fileStorageProvider
	 * @param \Latte\Engine                                                  $engine
	 * @param array                                                          $customFunctionNames
	 *
	 * @return void
	 */
	public static function register(FileStorageProviderInterface $fileStorageProvider, Engine $engine, array $customFunctionNames = []): void
	{
		$me = new static($fileStorageProvider);

		foreach (array_merge(self::DEFAULT_FUNCTION_NAMES, $customFunctionNames) as $functionId => $functionName) {
			$engine->addFunction($functionName, [$me, self::FUNCTION_CALLBACKS[$functionId]]);
		}
	}

	/**
	 * @param string|\SixtyEightPublishers\FileStorage\PathInfoInterface $pathInfo
	 * @param string|NULL                                                $storageName
	 *
	 * @return \SixtyEightPublishers\FileStorage\FileInfoInterface
	 */
	public function createFileInfo($pathInfo, ?string $storageName = NULL): FileInfoInterface
	{
		if ($pathInfo instanceof FileInfoInterface) {
			return $pathInfo;
		}

		$storage = $this->fileStorageProvider->get($storageName);

		if (!$pathInfo instanceof PathInfoInterface) {
			$pathInfo = $storage->createPathInfo((string) $pathInfo);
		}

		return $storage->createFileInfo($pathInfo);
	}
}
