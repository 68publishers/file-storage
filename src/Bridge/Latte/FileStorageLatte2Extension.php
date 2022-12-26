<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Latte;

use Latte\Engine;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;

final class FileStorageLatte2Extension
{
	private function __construct()
	{
	}

	public static function extend(Engine $engine, FileStorageProviderInterface $fileStorageProvider): void
	{
		foreach (FileStorageFunctionSet::functions($fileStorageProvider) as $functionName => $functionCallback) {
			$engine->addFunction($functionName, $functionCallback);
		}
	}
}
