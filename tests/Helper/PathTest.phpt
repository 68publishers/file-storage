<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Helper;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\FileStorage\Helper\Path;

require __DIR__ . '/../bootstrap.php';

final class PathTest extends TestCase
{
	public function testFullPathShouldBeParsed(): void
	{
		Assert::same([
			'var/www',
			'file',
			'json',
		], Path::parse('var/www/file.json'));
	}

	public function testPathWithoutDirectoryShouldBeParsed(): void
	{
		Assert::same([
			'',
			'file',
			'json',
		], Path::parse('file.json'));
	}

	public function testPathWithoutExtensionShouldBeParsed(): void
	{
		Assert::same([
			'var',
			'file',
			null,
		], Path::parse('var/file'));
	}

	public function testPathWithoutDirectoryAndExtensionShouldBeParsed(): void
	{
		Assert::same([
			'',
			'file',
			null,
		], Path::parse('file'));
	}

	public function testDotFileShouldBeParsed(): void
	{
		Assert::same([
			'var/www',
			'.env',
			null,
		], Path::parse('var/www/.env'));
	}

	public function testDotFileWithoutDirectoryShouldBeParsed(): void
	{
		Assert::same([
			'',
			'.env',
			null,
		], Path::parse('.env'));
	}
}

(new PathTest())->run();
