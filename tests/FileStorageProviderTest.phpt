<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Helper;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\FileStorage\FileStorageProvider;
use SixtyEightPublishers\FileStorage\FileStorageInterface;
use SixtyEightPublishers\FileStorage\Exception\InvalidArgumentException;

require __DIR__ . '/bootstrap.php';

final class FileStorageProviderTest extends TestCase
{
	public function testExceptionShouldBeThrownIfStorageNotDefined(): void
	{
		$defaultFs = Mockery::mock(FileStorageInterface::class);

		$defaultFs->shouldReceive('getName')
			->once()
			->andReturn('default');

		$provider = new FileStorageProvider($defaultFs, []);

		Assert::exception(
			static fn () => $provider->get('missing'),
			InvalidArgumentException::class,
			'FileStorage with name "missing" is not defined.'
		);
	}

	public function testDefaultStorageShouldBeReturned(): void
	{
		$defaultFs = Mockery::mock(FileStorageInterface::class);

		$defaultFs->shouldReceive('getName')
			->once()
			->andReturn('default');

		$provider = new FileStorageProvider($defaultFs, []);

		Assert::same($defaultFs, $provider->get());
	}

	public function testStorageShouldBeReturnedByName(): void
	{
		$defaultFs = Mockery::mock(FileStorageInterface::class);
		$otherFs = Mockery::mock(FileStorageInterface::class);

		$defaultFs->shouldReceive('getName')
			->once()
			->andReturn('default');

		$otherFs->shouldReceive('getName')
			->once()
			->andReturn('other');

		$provider = new FileStorageProvider($defaultFs, [$otherFs]);

		Assert::same($defaultFs, $provider->get('default'));
		Assert::same($otherFs, $provider->get('other'));
	}

	public function testProviderShouldBeIterable(): void
	{
		$defaultFs = Mockery::mock(FileStorageInterface::class);
		$otherFs = Mockery::mock(FileStorageInterface::class);

		$defaultFs->shouldReceive('getName')
			->once()
			->andReturn('default');

		$otherFs->shouldReceive('getName')
			->once()
			->andReturn('other');

		$provider = new FileStorageProvider($defaultFs, [$otherFs]);
		$results = [];

		foreach ($provider as $name => $storage) {
			$results[$name] = $storage;
		}

		Assert::same([
			'default' => $defaultFs,
			'other' => $otherFs,
		], $results);
	}

	protected function tearDown(): void
	{
		Mockery::close();
	}
}

(new FileStorageProviderTest())->run();
