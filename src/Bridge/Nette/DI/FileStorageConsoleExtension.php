<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Nette\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Reference;
use Nette\DI\Definitions\ServiceDefinition;
use SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Command\CleanCommand;
use SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Command\CopyAssetsCommand;
use SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Configurator\BaseCleanCommandConfigurator;
use SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Configurator\CleanCommandConfiguratorInterface;
use SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Configurator\CleanCommandConfiguratorRegistry;
use SixtyEightPublishers\FileStorage\Exception\RuntimeException;
use function array_keys;
use function array_map;
use function assert;
use function count;
use function sprintf;

final class FileStorageConsoleExtension extends CompilerExtension
{
    public const TAG_CLEAN_COMMAND_CONFIGURATOR = '68publishers.file_storage.console.clean_command_configurator';

    public function loadConfiguration(): void
    {
        if (0 >= count($this->compiler->getExtensions(FileStorageExtension::class))) {
            throw new RuntimeException(sprintf(
                'The extension %s can be used only with %s.',
                self::class,
                FileStorageExtension::class,
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
            ->setAutowired(false);

        $builder->addDefinition($this->prefix('command.clean'))
            ->setType(CleanCommand::class)
            ->setArgument('cleanCommandConfigurator', new Reference($this->prefix('configurator.clean_command.registry')));

        # Copy assets command
        $builder->addDefinition($this->prefix('command.copy_assets'))
            ->setType(CopyAssetsCommand::class);
    }

    public function beforeCompile(): void
    {
        $builder = $this->getContainerBuilder();

        $cleanCommandConfiguratorRegistry = $builder->getDefinition($this->prefix('configurator.clean_command.registry'));
        assert($cleanCommandConfiguratorRegistry instanceof ServiceDefinition);

        $cleanCommandConfiguratorRegistry->setArguments([
            array_map(static fn (string $name): Reference => new Reference($name), array_keys($builder->findByTag(self::TAG_CLEAN_COMMAND_CONFIGURATOR))),
        ]);
    }
}
