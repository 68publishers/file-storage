<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Nette\DI;

use Latte\Engine;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\PhpLiteral;
use Nette\DI\Definitions\FactoryDefinition;
use SixtyEightPublishers\FileStorage\Exception\RuntimeException;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;
use SixtyEightPublishers\FileStorage\Bridge\Latte\FileStorageFunctions;

final class FileStorageLatteExtension extends CompilerExtension
{
	/**
	 * {@inheritDoc}
	 */
	public function getConfigSchema(): Schema
	{
		$functionNames = [];

		foreach (FileStorageFunctions::DEFAULT_FUNCTION_NAMES as $functionId => $defaultFunctionName) {
			$functionNames[$functionId] = Expect::string($defaultFunctionName);
		}

		return Expect::structure([
			'function_names' => Expect::structure($functionNames),
			#   create_file_info: file_info
		]);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \SixtyEightPublishers\FileStorage\Exception\RuntimeException
	 */
	public function loadConfiguration(): void
	{
		if (0 >= count($this->compiler->getExtensions(FileStorageExtension::class))) {
			throw new RuntimeException(sprintf(
				'The extension %s can be used only with %s.',
				static::class,
				FileStorageExtension::class
			));
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		$latteFactory = $builder->getDefinition($builder->getByType(Engine::class) ?? 'nette.latteFactory');

		if ($latteFactory instanceof FactoryDefinition) {
			$latteFactory = $latteFactory->getResultDefinition();
		}

		$latteFactory->addSetup('?::register(?, ?, ?)', [
			new PhpLiteral(FileStorageFunctions::class),
			'@' . FileStorageProviderInterface::class,
			'@self',
			(array) $this->config->function_names,
		]);
	}
}
