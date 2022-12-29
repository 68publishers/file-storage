<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Bridge\Nette\DI;

use Tester\Assert;
use Tester\TestCase;
use Nette\DI\Container;
use Latte\Loaders\StringLoader;
use Tester\CodeCoverage\Collector;
use Nette\Bridges\ApplicationLatte\LatteFactory;
use SixtyEightPublishers\FileStorage\Exception\RuntimeException;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;
use function assert;

require __DIR__ . '/../../../bootstrap.php';

final class FileStorageLatteExtensionTest extends TestCase
{
	public function testExceptionShouldBeThrownIfFileStorageExtensionNotRegistered(): void
	{
		Assert::exception(
			static fn () => ContainerFactory::create(__DIR__ . '/config/FileStorageLatte/config.error.missingFileStorageExtension.neon'),
			RuntimeException::class,
			"The extension SixtyEightPublishers\FileStorage\Bridge\Nette\DI\FileStorageLatteExtension can be used only with SixtyEightPublishers\FileStorage\Bridge\Nette\DI\FileStorageExtension."
		);
	}

	public function testFileInfoFunctionWithStringPath(): void
	{
		$container = ContainerFactory::create(__DIR__ . '/config/FileStorageLatte/config.neon');

		$this->assertLatte($container, [
			'<img src="{file_info(\'static/file.png\')}" alt="">' => '<img src="/files/static/file.png" alt="">',
		]);
	}

	public function testFileInfoFunctionWithStringPathAndStorageName(): void
	{
		$container = ContainerFactory::create(__DIR__ . '/config/FileStorageLatte/config.neon');

		$this->assertLatte($container, [
			'<img src="{file_info(\'static/file.png\', \'other\')}" alt="">' => '<img src="https://www.example.com/data/static/file.png" alt="">',
		]);
	}

	public function testFileInfoFunctionWithPathInfo(): void
	{
		$container = ContainerFactory::create(__DIR__ . '/config/FileStorageLatte/config.neon');
		$fsProvider = $container->getByType(FileStorageProviderInterface::class);
		assert($fsProvider instanceof FileStorageProviderInterface);

		$this->assertLatte($container, [
			'<img src="{file_info($pathInfo)->withNamespace(\'aaa\')}" alt="">' => '<img src="/files/aaa/file.png" alt="">',
		], [
			'pathInfo' => $fsProvider->get()->createPathInfo('static/file.png'),
		]);
	}

	public function testFileInfoFunctionWithPathInfoAndStorageName(): void
	{
		$container = ContainerFactory::create(__DIR__ . '/config/FileStorageLatte/config.neon');
		$fsProvider = $container->getByType(FileStorageProviderInterface::class);
		assert($fsProvider instanceof FileStorageProviderInterface);

		$this->assertLatte($container, [
			'<img src="{file_info($pathInfo, \'other\')->withNamespace(\'aaa\')}" alt="">' => '<img src="https://www.example.com/data/aaa/file.png" alt="">',
		], [
			'pathInfo' => $fsProvider->get('other')->createPathInfo('static/file.png'),
		]);
	}

	public function testFileInfoFunctionWithFileInfo(): void
	{
		$container = ContainerFactory::create(__DIR__ . '/config/FileStorageLatte/config.neon');
		$fsProvider = $container->getByType(FileStorageProviderInterface::class);
		assert($fsProvider instanceof FileStorageProviderInterface);

		$fs = $fsProvider->get('default');

		$this->assertLatte($container, [
			'<img src="{file_info($fileInfo)}" alt="">' => '<img src="/files/static/file.png" alt="">',
		], [
			'fileInfo' => $fs->createFileInfo($fs->createPathInfo('static/file.png')),
		]);
	}

	/**
	 * Storage name argument is ignored because FileInfo is pre-created with the default storage
	 */
	public function testFileInfoFunctionWithFileInfoAndStorageName(): void
	{
		$container = ContainerFactory::create(__DIR__ . '/config/FileStorageLatte/config.neon');
		$fsProvider = $container->getByType(FileStorageProviderInterface::class);
		assert($fsProvider instanceof FileStorageProviderInterface);

		$fs = $fsProvider->get('default');

		$this->assertLatte($container, [
			'<img src="{file_info($fileInfo, \'other\')}" alt="">' => '<img src="/files/static/file.png" alt="">',
		], [
			'fileInfo' => $fs->createFileInfo($fs->createPathInfo('static/file.png')),
		]);
	}

	protected function tearDown(): void
	{
		# save manually partial code coverage to free memory
		if (Collector::isStarted()) {
			Collector::save();
		}
	}

	private function assertLatte(Container $container, array $assertions, array $params = []): void
	{
		$latteFactory = $container->getByType(LatteFactory::class);
		assert($latteFactory instanceof LatteFactory);
		$engine = $latteFactory->create();

		$engine->setLoader(new StringLoader());

		foreach ($assertions as $latteCode => $expected) {
			$rendered = $engine->renderToString($latteCode, $params);

			Assert::contains($expected, $rendered);
		}
	}
}

(new FileStorageLatteExtensionTest())->run();
