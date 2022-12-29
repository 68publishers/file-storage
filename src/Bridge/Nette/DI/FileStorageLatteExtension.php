<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Nette\DI;

use Latte\Engine;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Reference;
use Nette\DI\Definitions\Statement;
use Nette\DI\Definitions\FactoryDefinition;
use SixtyEightPublishers\FileStorage\Exception\RuntimeException;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;
use SixtyEightPublishers\FileStorage\Bridge\Latte\FileStorageLatteExtension as LatteExtension;
use function count;
use function assert;
use function sprintf;

final class FileStorageLatteExtension extends CompilerExtension
{
	public function loadConfiguration(): void
	{
		if (0 >= count($this->compiler->getExtensions(FileStorageExtension::class))) {
			throw new RuntimeException(sprintf(
				'The extension %s can be used only with %s.',
				self::class,
				FileStorageExtension::class
			));
		}
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		$latteFactory = $builder->getDefinition($builder->getByType(Engine::class) ?? 'nette.latteFactory');
		assert($latteFactory instanceof FactoryDefinition);
		$resultDefinition = $latteFactory->getResultDefinition();

		$resultDefinition->addSetup('addExtension', [
			new Statement(LatteExtension::class, [
				new Reference(FileStorageProviderInterface::class),
			]),
		]);
	}
}
