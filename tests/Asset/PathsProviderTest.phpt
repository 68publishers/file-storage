<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Asset;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\FileStorage\Asset\PathsProvider;

require __DIR__ . '/../bootstrap.php';

final class PathsProviderTest extends TestCase
{
	public function testPathsShouldBeProvided(): void
	{
		$provider = new PathsProvider();

		$provider->addPaths('a', [
			'static/file1.json' => 'destination/file1.json',
			'static/file2.json' => 'destination/file2.json',
		]);

		$provider->addPaths('b', [
			'images' => 'destination/images',
		]);

		Assert::same([
			'static/file1.json' => 'destination/file1.json',
			'static/file2.json' => 'destination/file2.json',
		], $provider->getPaths('a'));

		Assert::same([
			'images' => 'destination/images',
		], $provider->getPaths('b'));

		Assert::same([], $provider->getPaths('missing'));
	}
}

(new PathsProviderTest())->run();
