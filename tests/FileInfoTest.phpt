<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Helper;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\FileStorage\FileInfo;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use SixtyEightPublishers\FileStorage\LinkGenerator\LinkGeneratorInterface;

require __DIR__ . '/bootstrap.php';

final class FileInfoTest extends TestCase
{
	public function testNamespaceShouldBeChanged(): void
	{
		$linkGenerator = Mockery::mock(LinkGeneratorInterface::class);
		$pathInfo = Mockery::mock(PathInfoInterface::class);
		$pathInfo2 = Mockery::mock(PathInfoInterface::class);

		$pathInfo->shouldReceive('withNamespace')
			->once()
			->with('var/example')
			->andReturn($pathInfo2);

		$pathInfo->shouldReceive('getNamespace')
			->once()
			->andReturn('var/www');

		$pathInfo2->shouldReceive('getNamespace')
			->once()
			->andReturn('var/example');

		$fileInfo = new FileInfo($linkGenerator, $pathInfo, 'default');
		$newFileInfo = $fileInfo->withNamespace('var/example');

		Assert::notSame($fileInfo, $newFileInfo);
		Assert::equal('var/www', $fileInfo->getNamespace());
		Assert::equal('var/example', $newFileInfo->getNamespace());
	}

	public function testNameShouldBeChanged(): void
	{
		$linkGenerator = Mockery::mock(LinkGeneratorInterface::class);
		$pathInfo = Mockery::mock(PathInfoInterface::class);
		$pathInfo2 = Mockery::mock(PathInfoInterface::class);

		$pathInfo->shouldReceive('withName')
			->once()
			->with('new_file')
			->andReturn($pathInfo2);

		$pathInfo->shouldReceive('getName')
			->once()
			->andReturn('file');

		$pathInfo2->shouldReceive('getName')
			->once()
			->andReturn('new_file');

		$fileInfo = new FileInfo($linkGenerator, $pathInfo, 'default');
		$newFileInfo = $fileInfo->withName('new_file');

		Assert::notSame($fileInfo, $newFileInfo);
		Assert::equal('file', $fileInfo->getName());
		Assert::equal('new_file', $newFileInfo->getName());
	}

	public function testExtensionShouldBeChanged(): void
	{
		$linkGenerator = Mockery::mock(LinkGeneratorInterface::class);
		$pathInfo = Mockery::mock(PathInfoInterface::class);
		$pathInfo2 = Mockery::mock(PathInfoInterface::class);
		$pathInfo3 = Mockery::mock(PathInfoInterface::class);

		$pathInfo->shouldReceive('withExtension')
			->once()
			->with('png')
			->andReturn($pathInfo2);

		$pathInfo->shouldReceive('withExt')
			->once()
			->with(null)
			->andReturn($pathInfo3);

		$pathInfo->shouldReceive('getExtension')
			->once()
			->andReturn('json');

		$pathInfo2->shouldReceive('getExtension')
			->once()
			->andReturn('png');

		$pathInfo3->shouldReceive('getExtension')
			->once()
			->andReturn(null);

		$fileInfo = new FileInfo($linkGenerator, $pathInfo, 'default');
		$pngFileInfo = $fileInfo->withExtension('png');
		$nullFileInfo = $fileInfo->withExt(null);

		Assert::notSame($fileInfo, $pngFileInfo);
		Assert::notSame($fileInfo, $nullFileInfo);
		Assert::equal('json', $fileInfo->getExtension());
		Assert::equal('png', $pngFileInfo->getExtension());
		Assert::null($nullFileInfo->getExtension());
	}

	public function testVersionShouldBeChanged(): void
	{
		$linkGenerator = Mockery::mock(LinkGeneratorInterface::class);
		$pathInfo = Mockery::mock(PathInfoInterface::class);
		$pathInfo2 = Mockery::mock(PathInfoInterface::class);

		$pathInfo->shouldReceive('withVersion')
			->once()
			->with('123')
			->andReturn($pathInfo2);

		$pathInfo->shouldReceive('getVersion')
			->once()
			->andReturn(null);

		$pathInfo2->shouldReceive('getVersion')
			->once()
			->andReturn('123');

		$fileInfo = new FileInfo($linkGenerator, $pathInfo, 'default');
		$versionedFileInfo = $fileInfo->withVersion('123');

		Assert::notSame($fileInfo, $versionedFileInfo);
		Assert::equal(null, $fileInfo->getVersion());
		Assert::equal('123', $versionedFileInfo->getVersion());
	}

	public function testPathShouldBeReturned(): void
	{
		$linkGenerator = Mockery::mock(LinkGeneratorInterface::class);
		$pathInfo = Mockery::mock(PathInfoInterface::class);

		$pathInfo->shouldReceive('getPath')
			->once()
			->andReturn('var/www/file.json');

		$fileInfo = new FileInfo($linkGenerator, $pathInfo, 'default');

		Assert::same('var/www/file.json', $fileInfo->getPath());
	}

	public function testFileInfoShouldBeConvertedToString(): void
	{
		$linkGenerator = Mockery::mock(LinkGeneratorInterface::class);
		$pathInfo = Mockery::mock(PathInfoInterface::class);

		$linkGenerator->shouldReceive('link')
			->once()
			->with($pathInfo)
			->andReturn('https://www.example.com/files/file.json');

		$fileInfo = new FileInfo($linkGenerator, $pathInfo, 'default');

		Assert::same('https://www.example.com/files/file.json', (string) $fileInfo);
	}

	public function testLinkShouldBeReturned(): void
	{
		$linkGenerator = Mockery::mock(LinkGeneratorInterface::class);
		$pathInfo = Mockery::mock(PathInfoInterface::class);

		$linkGenerator->shouldReceive('link')
			->once()
			->with($pathInfo)
			->andReturn('https://www.example.com/files/file.json');

		$fileInfo = new FileInfo($linkGenerator, $pathInfo, 'default');

		Assert::same('https://www.example.com/files/file.json', $fileInfo->link());
	}

	public function testFileInfoShouldBeSerializedIntoJson(): void
	{
		$linkGenerator = Mockery::mock(LinkGeneratorInterface::class);
		$pathInfo = Mockery::mock(PathInfoInterface::class);

		$pathInfo->shouldReceive('getPath')
			->once()
			->andReturn('var/www/file.json');

		$pathInfo->shouldReceive('getVersion')
			->once()
			->andReturn('123');

		$fileInfo = new FileInfo($linkGenerator, $pathInfo, 'default');

		Assert::same('{"path":"var\/www\/file.json","storage":"default","version":"123"}', json_encode($fileInfo, JSON_THROW_ON_ERROR));
	}

	public function testStorageNameShouldBeReturned(): void
	{
		$linkGenerator = Mockery::mock(LinkGeneratorInterface::class);
		$pathInfo = Mockery::mock(PathInfoInterface::class);
		$fileInfo = new FileInfo($linkGenerator, $pathInfo, 'default');

		Assert::same('default', $fileInfo->getStorageName());
	}

	protected function tearDown(): void
	{
		Mockery::close();
	}
}

(new FileInfoTest())->run();
