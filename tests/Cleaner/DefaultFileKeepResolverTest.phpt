<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Cleaner;

use SixtyEightPublishers\FileStorage\Cleaner\DefaultFileKeepResolver;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

final class DefaultFileKeepResolverTest extends TestCase
{
    public function testResolveKeptFiles(): void
    {
        $resolver = new DefaultFileKeepResolver();

        Assert::true($resolver->isKept('.gitignore'));
        Assert::true($resolver->isKept('.gitkeep'));
        Assert::false($resolver->isKept('test'));
    }
}

(new DefaultFileKeepResolverTest())->run();
