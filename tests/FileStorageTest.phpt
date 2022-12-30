<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use League\Flysystem\FilesystemOperator;
use SixtyEightPublishers\FileStorage\FileStorage;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use SixtyEightPublishers\FileStorage\Config\ConfigInterface;
use SixtyEightPublishers\FileStorage\Resource\ResourceInterface;
use SixtyEightPublishers\FileStorage\Resource\ResourceFactoryInterface;
use SixtyEightPublishers\FileStorage\Persistence\FilePersisterInterface;
use SixtyEightPublishers\FileStorage\LinkGenerator\LinkGeneratorInterface;

require __DIR__ . '/bootstrap.php';

final class FileStorageTest extends TestCase
{
	public function testFilesystemShouldBeReturned(): void
	{
		$filesystem = Mockery::mock(FilesystemOperator::class);
		$filePersister = Mockery::mock(FilePersisterInterface::class);

		$filePersister->shouldReceive('getFilesystem')
			->once()
			->andReturn($filesystem);

		$storage = $this->createFileStorage(filePersister: $filePersister);

		Assert::same($filesystem, $storage->getFilesystem());
	}

	public function testPathInfoShouldExists(): void
	{
		$pathInfo = Mockery::mock(PathInfoInterface::class);
		$filePersister = Mockery::mock(FilePersisterInterface::class);

		$filePersister->shouldReceive('exists')
			->once()
			->with($pathInfo)
			->andReturn(true);

		$storage = $this->createFileStorage(filePersister: $filePersister);

		Assert::true($storage->exists($pathInfo));
	}

	public function testResourceShouldBeSaved(): void
	{
		$resource = Mockery::mock(ResourceInterface::class);
		$filePersister = Mockery::mock(FilePersisterInterface::class);

		$filePersister->shouldReceive('save')
			->once()
			->with($resource, [])
			->andReturn('var/www/file.json');

		$storage = $this->createFileStorage(filePersister: $filePersister);

		Assert::same('var/www/file.json', $storage->save($resource, []));
	}

	public function testPathInfoShouldBeDeleted(): void
	{
		$pathInfo = Mockery::mock(PathInfoInterface::class);
		$filePersister = Mockery::mock(FilePersisterInterface::class);

		$filePersister->shouldReceive('delete')
			->once()
			->with($pathInfo, [])
			->andReturns();

		$storage = $this->createFileStorage(filePersister: $filePersister);
		$storage->delete($pathInfo, []);
	}

	public function testNameShouldBeReturned(): void
	{
		$storage = $this->createFileStorage();

		Assert::same('default', $storage->getName());
	}

	public function testConfigShouldBeReturned(): void
	{
		$config = Mockery::mock(ConfigInterface::class);
		$storage = $this->createFileStorage(config: $config);

		Assert::same($config, $storage->getConfig());
	}

	public function testPathInfoShouldBeCreated(): void
	{
		$storage = $this->createFileStorage();
		$pathInfo = $storage->createPathInfo('var/www/file.json');

		Assert::same('var/www/file.json', $pathInfo->getPath());
	}

	public function testFileInfoShouldBeCreated(): void
	{
		$storage = $this->createFileStorage();
		$pathInfo = $storage->createPathInfo('var/www/file.json');
		$fileInfo = $storage->createFileInfo($pathInfo);

		Assert::same('var/www/file.json', $fileInfo->getPath());
		Assert::same('default', $fileInfo->getStorageName());
	}

	public function testLinkShouldBeReturned(): void
	{
		$linkGenerator = Mockery::mock(LinkGeneratorInterface::class);
		$storage = $this->createFileStorage(linkGenerator: $linkGenerator);
		$pathInfo = $storage->createPathInfo('var/www/file.json');

		$linkGenerator->shouldReceive('link')
			->once()
			->with($pathInfo)
			->andReturns('https://www.example.com/var/www/file.json');

		Assert::same('https://www.example.com/var/www/file.json', $storage->link($pathInfo));
	}

	public function testResourceShouldBeCreated(): void
	{
		$resourceFactory = Mockery::mock(ResourceFactoryInterface::class);
		$resource = Mockery::mock(ResourceInterface::class);
		$storage = $this->createFileStorage(resourceFactory: $resourceFactory);
		$pathInfo = $storage->createPathInfo('var/www/file.json');

		$resourceFactory->shouldReceive('createResource')
			->once()
			->with($pathInfo)
			->andReturns($resource);

		Assert::same($resource, $storage->createResource($pathInfo));
	}

	public function testResourceFromLocalFileShouldBeCreated(): void
	{
		$resourceFactory = Mockery::mock(ResourceFactoryInterface::class);
		$resource = Mockery::mock(ResourceInterface::class);
		$storage = $this->createFileStorage(resourceFactory: $resourceFactory);
		$pathInfo = $storage->createPathInfo('var/www/file.json');

		$resourceFactory->shouldReceive('createResourceFromLocalFile')
			->once()
			->with($pathInfo, 'var/www/file.json')
			->andReturns($resource);

		Assert::same($resource, $storage->createResourceFromLocalFile($pathInfo, 'var/www/file.json'));
	}

	protected function tearDown(): void
	{
		Mockery::close();
	}

	private function createFileStorage(
		?ConfigInterface $config = null,
		?ResourceFactoryInterface $resourceFactory = null,
		?LinkGeneratorInterface $linkGenerator = null,
		?FilePersisterInterface $filePersister = null,
	): FileStorage {
		$config = $config ?? Mockery::mock(ConfigInterface::class);
		$resourceFactory = $resourceFactory ?? Mockery::mock(ResourceFactoryInterface::class);
		$linkGenerator = $linkGenerator ?? Mockery::mock(LinkGeneratorInterface::class);
		$filePersister = $filePersister ?? Mockery::mock(FilePersisterInterface::class);

		return new FileStorage('default', $config, $resourceFactory, $linkGenerator, $filePersister);
	}
}

(new FileStorageTest())->run();
