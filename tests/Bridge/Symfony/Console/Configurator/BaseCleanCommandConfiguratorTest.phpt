<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Bridge\Symfony\Console\Configurator;

use Mockery;
use SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Configurator\BaseCleanCommandConfigurator;
use SixtyEightPublishers\FileStorage\Cleaner\StorageCleanerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../../../bootstrap.php';

final class BaseCleanCommandConfiguratorTest extends TestCase
{
    public function testOptionsShouldBeSet(): void
    {
        $command = Mockery::mock(Command::class);

        $command->shouldReceive('addOption')
            ->once()
            ->with(StorageCleanerInterface::OPTION_NAMESPACE, null, InputOption::VALUE_REQUIRED, 'Search only in a specific namespace');

        $configurator = new BaseCleanCommandConfigurator();

        $configurator->setupOptions($command);
    }

    public function testEmptyCleanerOptionsShouldBeReturned(): void
    {
        $input = Mockery::mock(InputInterface::class);

        $input->shouldReceive('getOption')
            ->once()
            ->with(StorageCleanerInterface::OPTION_NAMESPACE)
            ->andReturn(null);

        $configurator = new BaseCleanCommandConfigurator();
        $options = $configurator->getCleanerOptions($input);

        Assert::same([], $options);
    }

    public function testCleanerOptionsShouldBeReturned(): void
    {
        $input = Mockery::mock(InputInterface::class);

        $input->shouldReceive('getOption')
            ->once()
            ->with(StorageCleanerInterface::OPTION_NAMESPACE)
            ->andReturn('static');

        $configurator = new BaseCleanCommandConfigurator();
        $options = $configurator->getCleanerOptions($input);

        Assert::same([
            StorageCleanerInterface::OPTION_NAMESPACE => 'static',
        ], $options);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

(new BaseCleanCommandConfiguratorTest())->run();
