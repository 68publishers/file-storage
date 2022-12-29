<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Bridge\Symfony\Console\Command;

use Mockery;
use ArrayIterator;
use Tester\Assert;
use Tester\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use SixtyEightPublishers\FileStorage\FileStorageInterface;
use SixtyEightPublishers\FileStorage\Asset\AssetsCopierInterface;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;
use SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Command\CopyAssetsCommand;
use function assert;

require __DIR__ . '/../../../../bootstrap.php';

final class CopyAssetsCommandTest extends TestCase
{
	public function testAssetsFromAllStoragesShouldBeCopied(): void
	{
		$provider = Mockery::mock(FileStorageProviderInterface::class);
		$storage = Mockery::mock(FileStorageInterface::class);
		$copier = Mockery::mock(AssetsCopierInterface::class);

		$provider->shouldReceive('getIterator')
			->once()
			->andReturn(new ArrayIterator([
				'default' => $storage,
			]));

		$copier->shouldReceive('copy')
			->once()
			->with($storage, Mockery::type(LoggerInterface::class))
			->andReturns();

		$tester = $this->createCommandTester($provider, $copier);

		$tester->execute([]);

		Assert::same(0, $tester->getStatusCode());
	}

	public function testAssetsFromSpecifiedStoragesShouldBeCopied(): void
	{
		$provider = Mockery::mock(FileStorageProviderInterface::class);
		$storage = Mockery::mock(FileStorageInterface::class);
		$copier = Mockery::mock(AssetsCopierInterface::class);

		$provider->shouldReceive('get')
			->once()
			->with('default')
			->andReturn($storage);

		$copier->shouldReceive('copy')
			->once()
			->with($storage, Mockery::type(LoggerInterface::class))
			->andReturns();

		$tester = $this->createCommandTester($provider, $copier);

		$tester->execute([
			'storage' => 'default',
		]);

		Assert::same(0, $tester->getStatusCode());
	}

	protected function tearDown(): void
	{
		Mockery::close();
	}

	private function createCommandTester(FileStorageProviderInterface $fileStorageProvider, AssetsCopierInterface $assetsCopier): CommandTester
	{
		$command = new CopyAssetsCommand($assetsCopier, $fileStorageProvider);
		$application = new Application();

		$application->add($command);

		$command = $application->find('file-storage:copy-assets');
		assert($command instanceof CopyAssetsCommand);

		return new CommandTester($command);
	}
}

(new CopyAssetsCommandTest())->run();
