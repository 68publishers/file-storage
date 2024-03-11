<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Bridge\Symfony\Console\Configurator;

use Mockery;
use SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Configurator\CleanCommandConfiguratorInterface;
use SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Configurator\CleanCommandConfiguratorRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../../../bootstrap.php';

final class CleanCommandConfiguratorRegistryTest extends TestCase
{
    public function testOptionsShouldBeSet(): void
    {
        $configurator1 = Mockery::mock(CleanCommandConfiguratorInterface::class);
        $configurator2 = Mockery::mock(CleanCommandConfiguratorInterface::class);
        $command = Mockery::mock(Command::class);

        $configurator1->shouldReceive('setupOptions')
            ->once()
            ->with($command)
            ->andReturns();

        $configurator2->shouldReceive('setupOptions')
            ->once()
            ->with($command)
            ->andReturns();

        $registry = new CleanCommandConfiguratorRegistry([$configurator1, $configurator2]);

        $registry->setupOptions($command);
    }

    public function testCleanerOptionsShouldBeReturned(): void
    {
        $configurator1 = Mockery::mock(CleanCommandConfiguratorInterface::class);
        $configurator2 = Mockery::mock(CleanCommandConfiguratorInterface::class);
        $input = Mockery::mock(InputInterface::class);

        $configurator1->shouldReceive('getCleanerOptions')
            ->once()
            ->with($input)
            ->andReturn([
                'a' => true,
            ]);

        $configurator2->shouldReceive('getCleanerOptions')
            ->once()
            ->with($input)
            ->andReturn([
                'b' => 12,
                'c' => 'test',
            ]);

        $registry = new CleanCommandConfiguratorRegistry([$configurator1, $configurator2]);
        $options = $registry->getCleanerOptions($input);

        Assert::same([
            'a' => true,
            'b' => 12,
            'c' => 'test',
        ], $options);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

(new CleanCommandConfiguratorRegistryTest())->run();
