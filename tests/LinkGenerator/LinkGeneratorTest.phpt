<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\LinkGenerator;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\FileStorage\Config\Config;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use SixtyEightPublishers\FileStorage\Config\ConfigInterface;
use SixtyEightPublishers\FileStorage\LinkGenerator\LinkGenerator;

require __DIR__ . '/../bootstrap.php';

final class LinkGeneratorTest extends TestCase
{
	public function testNonVersionedLinkShouldBeCreatedWithDefaultConfig(): void
	{
		$generator = new LinkGenerator(new Config([]));

		Assert::same('/var/www/file.json', $generator->link($this->createPathInfo('var/www/file.json', null)));
	}

	public function testVersionedLinkShouldBeCreatedWithDefaultConfig(): void
	{
		$generator = new LinkGenerator(new Config([]));

		Assert::same('/var/www/file.json?_v=123', $generator->link($this->createPathInfo('var/www/file.json', '123')));
	}

	public function testVersionedLinkShouldBeCreatedWithEmptyVersionParameterName(): void
	{
		$generator = new LinkGenerator(new Config([
			ConfigInterface::VERSION_PARAMETER_NAME => '',
		]));

		Assert::same('/var/www/file.json?123', $generator->link($this->createPathInfo('var/www/file.json', '123')));
	}

	public function testNonVersionedLinkShouldBeCreatedWithCustomBasePath(): void
	{
		$generator = new LinkGenerator(new Config([
			ConfigInterface::BASE_PATH => '/files/',
		]));

		Assert::same('/files/var/www/file.json', $generator->link($this->createPathInfo('var/www/file.json', null)));
	}

	public function testNonVersionedLinkShouldBeCreatedWithCustomHost(): void
	{
		$generator = new LinkGenerator(new Config([
			ConfigInterface::HOST => 'https://www.example.com',
		]));

		Assert::same('https://www.example.com/var/www/file.json', $generator->link($this->createPathInfo('var/www/file.json', null)));
	}

	public function testVersionedLinkShouldBeCreatedWithCustomHostAndBasePath(): void
	{
		$generator = new LinkGenerator(new Config([
			ConfigInterface::HOST => 'https://www.example.com',
			ConfigInterface::BASE_PATH => '/files/',
		]));

		Assert::same('https://www.example.com/files/var/www/file.json?_v=123', $generator->link($this->createPathInfo('var/www/file.json', '123')));
	}

	protected function tearDown(): void
	{
		Mockery::close();
	}

	private function createPathInfo(string $path, ?string $version): PathInfoInterface
	{
		$pathInfo = Mockery::mock(PathInfoInterface::class);

		$pathInfo->shouldReceive('getPath')
			->once()
			->andReturn($path);

		$pathInfo->shouldReceive('getVersion')
			->times(null === $version ? 1 : 2)
			->andReturn($version);

		return $pathInfo;
	}
}

(new LinkGeneratorTest())->run();
