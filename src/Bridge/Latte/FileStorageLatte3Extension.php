<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Latte;

use Latte\Extension;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;

final class FileStorageLatte3Extension extends Extension
{
	public function __construct(
		private readonly FileStorageProviderInterface $fileStorageProvider,
	) {
	}

	public function getFunctions(): array
	{
		return FileStorageFunctionSet::functions($this->fileStorageProvider);
	}
}
