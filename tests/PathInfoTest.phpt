<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Helper;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\FileStorage\PathInfo;
use SixtyEightPublishers\FileStorage\Exception\PathInfoException;

require __DIR__ . '/bootstrap.php';

final class PathInfoTest extends TestCase
{
	public function testExceptionShouldBeThrownIfPathInfoWithEmptyNameIsCreated(): void
	{
		Assert::exception(
			static fn () => new PathInfo('var/www', '', null),
			PathInfoException::class,
			'Given path "var/www/" is not valid path for SixtyEightPublishers\FileStorage\PathInfoInterface.'
		);
	}

	public function testExceptionShouldBeThrownIfEmptyNamePassed(): void
	{
		$info = new PathInfo('var/www', 'file', 'json');

		Assert::exception(
			static fn () => $info->withName(''),
			PathInfoException::class,
			'Given path "var/www/.json" is not valid path for SixtyEightPublishers\FileStorage\PathInfoInterface.'
		);
	}

	public function testNamespaceShouldBeChanged(): void
	{
		$info = new PathInfo('var/www', 'file', 'json');
		$newInfo = $info->withNamespace('var/example');

		Assert::notSame($info, $newInfo);
		Assert::equal('var/www', $info->getNamespace());
		Assert::equal('var/example', $newInfo->getNamespace());
	}

	public function testNameShouldBeChanged(): void
	{
		$info = new PathInfo('var/www', 'file', 'json');
		$newInfo = $info->withName('new_file');

		Assert::notSame($info, $newInfo);
		Assert::equal('file', $info->getName());
		Assert::equal('new_file', $newInfo->getName());
	}

	public function testExtensionShouldBeChanged(): void
	{
		$info = new PathInfo('var/www', 'file', 'json');
		$pngInfo = $info->withExtension('png');
		$nullInfo = $info->withExt(null);

		Assert::notSame($info, $pngInfo);
		Assert::notSame($info, $nullInfo);
		Assert::equal('json', $info->getExtension());
		Assert::equal('png', $pngInfo->getExtension());
		Assert::null($nullInfo->getExtension());
	}

	public function testVersionShouldBeChanged(): void
	{
		$info = new PathInfo('var/www', 'file', 'json');
		$versionedInfo = $info->withVersion('123');

		Assert::notSame($info, $versionedInfo);
		Assert::equal(null, $info->getVersion());
		Assert::equal('123', $versionedInfo->getVersion());
	}

	public function testPathShouldBeReturned(): void
	{
		$info = new PathInfo('var/www', 'file', 'json');
		$info2 = new PathInfo('', 'file', 'json');
		$info3 = new PathInfo('', 'file', null);
		$info4 = new PathInfo('var/www', 'file', null);

		Assert::same('var/www/file.json', $info->getPath());
		Assert::same('file.json', $info2->getPath());
		Assert::same('file', $info3->getPath());
		Assert::same('var/www/file', $info4->getPath());
	}

	public function testPathInfoShouldBeConvertedToString(): void
	{
		$info = new PathInfo('var/www', 'file', 'json');
		$info2 = new PathInfo('', 'file', 'json');
		$info3 = new PathInfo('', 'file', null);
		$info4 = new PathInfo('var/www', 'file', null);

		Assert::same('var/www/file.json', (string) $info);
		Assert::same('file.json', (string) $info2);
		Assert::same('file', (string) $info3);
		Assert::same('var/www/file', (string) $info4);
	}
}

(new PathInfoTest())->run();
