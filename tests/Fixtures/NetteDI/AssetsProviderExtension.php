<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Fixtures\NetteDI;

use Nette\DI\CompilerExtension;
use SixtyEightPublishers\FileStorage\Bridge\Nette\DI\Assets;
use SixtyEightPublishers\FileStorage\Bridge\Nette\DI\AssetsProviderInterface;

final class AssetsProviderExtension extends CompilerExtension implements AssetsProviderInterface
{
	public function provideAssets(): array
	{
		return [
			new Assets('default', [
				'test' => 'test',
			]),
		];
	}
}
