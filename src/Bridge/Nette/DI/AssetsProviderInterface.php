<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Nette\DI;

interface AssetsProviderInterface
{
	/**
	 * @return array<Assets>
	 */
	public function provideAssets(): array;
}
