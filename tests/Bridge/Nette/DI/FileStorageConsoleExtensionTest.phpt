<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Bridge\Nette\DI;

use Closure;
use SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Command\CleanCommand;
use SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Command\CopyAssetsCommand;
use SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Configurator\BaseCleanCommandConfigurator;
use SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Configurator\CleanCommandConfiguratorRegistry;
use SixtyEightPublishers\FileStorage\Exception\RuntimeException;
use SixtyEightPublishers\FileStorage\Tests\Fixtures\CustomCleanCommandConfigurator;
use Symfony\Component\Console\Application;
use Tester\Assert;
use Tester\CodeCoverage\Collector;
use Tester\TestCase;
use function assert;
use function call_user_func;
use function count;

require __DIR__ . '/../../../bootstrap.php';

final class FileStorageConsoleExtensionTest extends TestCase
{
    public function testExceptionShouldBeThrownIfFileStorageExtensionNotRegistered(): void
    {
        Assert::exception(
            static fn () => ContainerFactory::create(__DIR__ . '/config/FileStorageConsole/config.error.missingFileStorageExtension.neon'),
            RuntimeException::class,
            "The extension SixtyEightPublishers\FileStorage\Bridge\Nette\DI\FileStorageConsoleExtension can be used only with SixtyEightPublishers\FileStorage\Bridge\Nette\DI\FileStorageExtension.",
        );
    }

    public function testExtensionShouldBeIntegrated(): void
    {
        $container = ContainerFactory::create(__DIR__ . '/config/FileStorageConsole/config.neon');
        $application = $container->getByType(Application::class);
        assert($application instanceof Application);

        $this->assertCleanCommand($application, [
            BaseCleanCommandConfigurator::class,
        ]);
        $this->assertCopyAssetsCommand($application);
    }

    public function testExtensionShouldBeIntegratedWithCustomCleanCommandConfigurator(): void
    {
        $container = ContainerFactory::create(__DIR__ . '/config/FileStorageConsole/config.withCustomCleanCommandConfigurator.neon');
        $application = $container->getByType(Application::class);
        assert($application instanceof Application);

        $this->assertCleanCommand($application, [
            BaseCleanCommandConfigurator::class,
            CustomCleanCommandConfigurator::class,
        ]);
        $this->assertCopyAssetsCommand($application);
    }

    protected function tearDown(): void
    {
        # save manually partial code coverage to free memory
        if (Collector::isStarted()) {
            Collector::save();
        }
    }

    private function assertCleanCommand(Application $application, array $configuratorTypes): void
    {
        $command = $application->get('file-storage:clean');

        Assert::type(CleanCommand::class, $command);
        assert($command instanceof CleanCommand);

        call_user_func(Closure::bind(
            static function () use ($command, $configuratorTypes): void {
                $configurator = $command->cleanCommandConfigurator;

                Assert::type(CleanCommandConfiguratorRegistry::class, $configurator);
                assert($configurator instanceof CleanCommandConfiguratorRegistry);

                call_user_func(Closure::bind(
                    static function () use ($configurator, $configuratorTypes): void {
                        Assert::same(count($configuratorTypes), count($configurator->configurators));

                        foreach ($configuratorTypes as $index => $configuratorType) {
                            Assert::type($configuratorType, $configurator->configurators[$index]);
                        }
                    },
                    null,
                    CleanCommandConfiguratorRegistry::class,
                ));
            },
            null,
            CleanCommand::class,
        ));
    }

    private function assertCopyAssetsCommand(Application $application): void
    {
        Assert::type(CopyAssetsCommand::class, $application->get('file-storage:copy-assets'));
    }
}

(new FileStorageConsoleExtensionTest())->run();
