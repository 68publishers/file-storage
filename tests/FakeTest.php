<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests;

use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

final class FakeTest extends TestCase
{
	public function testFake(): void
	{
		Assert::true(true);
	}
}

(new FakeTest())->run();
