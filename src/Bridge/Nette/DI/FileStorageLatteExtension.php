<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Nette\DI;

use Latte\Engine;
use Nette\DI\ContainerBuilder;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Reference;
use Nette\DI\Definitions\Statement;
use Nette\DI\Definitions\FactoryDefinition;
use SixtyEightPublishers\FileStorage\Exception\RuntimeException;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;
use SixtyEightPublishers\FileStorage\Bridge\Latte\FileStorageLatte2Extension;
use SixtyEightPublishers\FileStorage\Bridge\Latte\FileStorageLatte3Extension;
use function count;
use function assert;
use function sprintf;
use function version_compare;

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

		if (version_compare(Engine::VERSION, '3', '<')) {
			$resultDefinition->addSetup('?::extend(?, ?, ?)', [
				ContainerBuilder::literal(FileStorageLatte2Extension::class),
				new Reference('self'),
				new Reference(FileStorageProviderInterface::class),
			]);

			return;
		}

		$resultDefinition->addSetup('addExtension', [
			new Statement(FileStorageLatte3Extension::class, [
				new Reference(FileStorageProviderInterface::class),
			]),
		]);
	}
}
