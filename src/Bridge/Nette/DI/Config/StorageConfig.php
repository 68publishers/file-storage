<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Nette\DI\Config;

final class StorageConfig
{
	/** @var array<string, mixed> */
	public array $config;

	public FilesystemConfig $filesystem;

	/** @var array<string, string> */
	public array $assets;
}
