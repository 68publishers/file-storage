<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Persistence;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use League\Flysystem\Filesystem;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToDeleteFile;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use SixtyEightPublishers\FileStorage\Resource\SimpleResource;
use SixtyEightPublishers\FileStorage\Persistence\FilePersister;
use SixtyEightPublishers\FileStorage\Exception\FilesystemException;
use SixtyEightPublishers\FileStorage\Persistence\FilePersisterInterface;
use function fopen;

require __DIR__ . '/../bootstrap.php';

final class FilePersisterTest extends TestCase
{
	public function testFilesystemShouldBeReturned(): void
	{
		$persister = new FilePersister($this->createFilesystem());

		Assert::type(FilesystemOperator::class, $persister->getFilesystem());
	}

	public function testPathShouldExists(): void
	{
		$pathInfo = Mockery::mock(PathInfoInterface::class);

		$pathInfo->shouldReceive('getPath')
			->once()
			->andReturn('var/www/file.json');

		$persister = new FilePersister($this->createFilesystem([
			'var/www/file.json' => '{}',
		]));

		Assert::true($persister->exists($pathInfo));
	}

	public function testPathShouldNotExists(): void
	{
		$pathInfo = Mockery::mock(PathInfoInterface::class);

		$pathInfo->shouldReceive('getPath')
			->once()
			->andReturn('var/www/file.json');

		$persister = new FilePersister($this->createFilesystem());

		Assert::false($persister->exists($pathInfo));
	}

	public function testPathShouldNotExistsOnFilesystemException(): void
	{
		$pathInfo = Mockery::mock(PathInfoInterface::class);

		$pathInfo->shouldReceive('getPath')
			->once()
			->andReturn('var/www/file.json');

		$filesystem = Mockery::instanceMock($this->createFilesystem());

		$filesystem->shouldReceive('fileExists')
			->once()
			->with('var/www/file.json')
			->andThrows(UnableToReadFile::fromLocation('var/www/file.json', 'test'));

		$persister = new FilePersister($filesystem);

		Assert::false($persister->exists($pathInfo));
	}

	public function testExceptionShouldBeThrownOnDeleteIfFilesystemExceptionIsThrown(): void
	{
		$pathInfo = Mockery::mock(PathInfoInterface::class);

		$pathInfo->shouldReceive('getPath')
			->once()
			->andReturn('var/www/file.json');

		$filesystem = Mockery::instanceMock($this->createFilesystem());

		$filesystem->shouldReceive('delete')
			->once()
			->with('var/www/file.json')
			->andThrows(UnableToDeleteFile::atLocation('var/www/file.json', 'test'));

		$persister = new FilePersister($filesystem);

		Assert::exception(
			static fn () => $persister->delete($pathInfo),
			FilesystemException::class,
			'Unable to delete file located at: var/www/file.json. test'
		);
	}

	public function testExceptionShouldNotBeThrownOnDeleteIfFilesystemExceptionIsThrownButExceptionAreSuppressed(): void
	{
		$pathInfo = Mockery::mock(PathInfoInterface::class);

		$pathInfo->shouldReceive('getPath')
			->once()
			->andReturn('var/www/file.json');

		$filesystem = Mockery::instanceMock($this->createFilesystem());

		$filesystem->shouldReceive('delete')
			->once()
			->with('var/www/file.json')
			->andThrows(UnableToDeleteFile::atLocation('var/www/file.json', 'test'));

		$persister = new FilePersister($filesystem);

		$persister->delete($pathInfo, [
			FilePersisterInterface::OPTION_SUPPRESS_EXCEPTIONS => true,
		]);
	}

	public function testFileShouldBeDeleted(): void
	{
		$pathInfo = Mockery::mock(PathInfoInterface::class);

		$pathInfo->shouldReceive('getPath')
			->once()
			->andReturn('var/www/file.json');

		$filesystem = $this->createFilesystem([
			'var/www/file.json' => '{}',
		]);
		$persister = new FilePersister($filesystem);

		Assert::true($filesystem->fileExists('var/www/file.json'));

		$persister->delete($pathInfo);

		Assert::false($filesystem->fileExists('var/www/file.json'));
	}

	public function testExceptionShouldBeThrownOnSaveIfFilesystemExceptionIsThrown(): void
	{
		$pathInfo = Mockery::mock(PathInfoInterface::class);

		$pathInfo->shouldReceive('getPath')
			->once()
			->andReturn('var/www/file.json');

		$filesystem = Mockery::instanceMock($this->createFilesystem());

		$filesystem->shouldReceive('write')
			->once()
			->with('var/www/file.json', '{}', [])
			->andThrows(UnableToWriteFile::atLocation('var/www/file.json', 'test'));

		$persister = new FilePersister($filesystem);

		Assert::exception(
			static fn () => $persister->save(new SimpleResource($pathInfo, '{}')),
			FilesystemException::class,
			'Unable to write file at location: var/www/file.json. test'
		);
	}

	public function testExceptionShouldNotBeThrownOnSaveIfFilesystemExceptionIsThrownButExceptionAreSuppressed(): void
	{
		$pathInfo = Mockery::mock(PathInfoInterface::class);

		$pathInfo->shouldReceive('getPath')
			->once()
			->andReturn('var/www/file.json');

		$filesystem = Mockery::instanceMock($this->createFilesystem());

		$filesystem->shouldReceive('write')
			->once()
			->with('var/www/file.json', '{}', [FilePersisterInterface::OPTION_SUPPRESS_EXCEPTIONS => true])
			->andThrows(UnableToWriteFile::atLocation('var/www/file.json', 'test'));

		$persister = new FilePersister($filesystem);

		$path = $persister->save(new SimpleResource($pathInfo, '{}'), [
			FilePersisterInterface::OPTION_SUPPRESS_EXCEPTIONS => true,
		]);

		Assert::same('var/www/file.json', $path);
	}

	public function testStringSourceShouldBeSaved(): void
	{
		$filename = 'var/www/file.json';
		$pathInfo = Mockery::mock(PathInfoInterface::class);

		$pathInfo->shouldReceive('getPath')
			->once()
			->andReturn($filename);

		$filesystem = $this->createFilesystem();
		$persister = new FilePersister($filesystem);

		$path = $persister->save(new SimpleResource($pathInfo, '{}'));

		Assert::same($filename, $path);
		Assert::true($filesystem->fileExists($filename));

		$contents = $filesystem->read($filename);

		Assert::same('{}', $contents);
	}

	public function testStreamSourceShouldBeSaved(): void
	{
		$filename = 'var/www/file.json';
		$pathInfo = Mockery::mock(PathInfoInterface::class);

		$pathInfo->shouldReceive('getPath')
			->once()
			->andReturn($filename);

		$filesystem = $this->createFilesystem();
		$persister = new FilePersister($filesystem);

		$path = $persister->save(new SimpleResource($pathInfo, fopen(__DIR__ . '/file.json', 'rb')));

		Assert::same($filename, $path);
		Assert::true($filesystem->fileExists($filename));

		$contents = $filesystem->read($filename);

		Assert::same("{\"abc\":123}\n", $contents);
	}

	private function createFilesystem(array $files = []): Filesystem
	{
		$fs = new Filesystem(
			new InMemoryFilesystemAdapter(),
		);

		foreach ($files as $filename => $content) {
			$fs->write($filename, $content);
		}

		return $fs;
	}

	protected function tearDown(): void
	{
		Mockery::close();
	}
}

(new FilePersisterTest())->run();
