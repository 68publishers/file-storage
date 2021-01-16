<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Nette\DI;

use Nette\DI\CompilerExtension;
use SixtyEightPublishers\FileStorage\Exception\RuntimeException;
use SixtyEightPublishers\FileStorage\Bridge\Console\Command\CleanCommand;
use SixtyEightPublishers\FileStorage\Bridge\Console\Command\CopyAssetsCommand;
use SixtyEightPublishers\FileStorage\Bridge\Console\Configurator\BaseCleanCommandConfigurator;
use SixtyEightPublishers\FileStorage\Bridge\Console\Configurator\CleanCommandConfiguratorRegistry;
use SixtyEightPublishers\FileStorage\Bridge\Console\Configurator\CleanCommandConfiguratorInterface;

final class FileStorageConsoleExtension extends CompilerExtension
{
	public const TAG_CLEAN_COMMAND_CONFIGURATOR = '68publishers.file_storage.console.clean_command_configurator';

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

		$builder = $this->getContainerBuilder();

		# Clean command
		$builder->addDefinition($this->prefix('configurator.clean_command.registry'))
			->setType(CleanCommandConfiguratorInterface::class)
			->setFactory(CleanCommandConfiguratorRegistry::class);

		$builder->addDefinition($this->prefix('configurator.clean_command.base'))
			->setType(CleanCommandConfiguratorInterface::class)
			->setFactory(BaseCleanCommandConfigurator::class)
			->addTag(self::TAG_CLEAN_COMMAND_CONFIGURATOR)
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('command.clean'))
			->setType(CleanCommand::class);

		# Copy assets command
		$builder->addDefinition($this->prefix('command.copy_assets'))
			->setType(CopyAssetsCommand::class);
	}

	/**
	 * {@inheritDoc}
	 */
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		/** @var \Nette\DI\Definitions\ServiceDefinition $cleanCommandConfiguratorRegistry */
		$cleanCommandConfiguratorRegistry = $builder->getDefinition($this->prefix('configurator.clean_command.registry'));

		$cleanCommandConfiguratorRegistry->setArguments([
			array_map(static function (string $name) {
				return '@' . $name;
			}, array_keys($builder->findByTag(self::TAG_CLEAN_COMMAND_CONFIGURATOR))),
		]);
	}
}
