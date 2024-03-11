<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Cleaner;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\FilesystemReader;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\Flysystem\MountManager;
use League\Flysystem\StorageAttributes;
use SixtyEightPublishers\FileStorage\Cleaner\DefaultFileKeepResolver;
use SixtyEightPublishers\FileStorage\Cleaner\StorageCleaner;
use SixtyEightPublishers\FileStorage\Cleaner\StorageCleanerInterface;
use Tester\Assert;
use Tester\TestCase;
use function count;

require __DIR__ . '/../bootstrap.php';

final class StorageCleanerTest extends TestCase
{
    private const FS_A_FILES = [
        '.gitkeep' => '',
        'var/cache1.txt' => '[["1"]]',
        'var/cache2.txt' => '[["2"]]',
        'var/nette/configurator/presenters.php' => 'return [];',
        'var/nette/container/ContainerA.php' => '<?php class ContainerA {}',
        'var/nette/container/ContainerB.php' => '<?php class ContainerB {}',
        'public/index.php' => '<?php echo "Hell world!";',
        'public/files/test.json' => '{"abc":123}',
    ];

    private const FS_B_FILES = [
        '.gitignore' => 'temp/',
        'temp/file1.php' => '<?php return 1;',
        'temp/file2.php' => '<?php return 2;',
    ];

    public function testFilesShouldBeCounted(): void
    {
        $cleaner = new StorageCleaner(new DefaultFileKeepResolver());
        $filesystem = $this->createFilesystem(self::FS_A_FILES);

        Assert::same(7, $cleaner->getCount($filesystem));

        Assert::same(5, $cleaner->getCount($filesystem, [
            StorageCleanerInterface::OPTION_NAMESPACE => 'var',
        ]));

        Assert::same(3, $cleaner->getCount($filesystem, [
            StorageCleanerInterface::OPTION_NAMESPACE => 'var/nette',
        ]));

        Assert::same(0, $cleaner->getCount($filesystem, [
            StorageCleanerInterface::OPTION_NAMESPACE => 'missing',
        ]));
    }

    public function testFilesShouldBeCountedInMount(): void
    {
        $cleaner = new StorageCleaner(new DefaultFileKeepResolver());
        $filesystem = new MountManager([
            'a' => $this->createFilesystem(self::FS_A_FILES),
            'b' => $this->createFilesystem(self::FS_B_FILES),
        ]);

        Assert::same(7, $cleaner->getCount($filesystem, [
            StorageCleanerInterface::OPTION_FILESYSTEM_PREFIX => 'a://',
        ]));

        Assert::same(2, $cleaner->getCount($filesystem, [
            StorageCleanerInterface::OPTION_FILESYSTEM_PREFIX => 'b://',
        ]));

        Assert::same(3, $cleaner->getCount($filesystem, [
            StorageCleanerInterface::OPTION_NAMESPACE => 'var/nette',
            StorageCleanerInterface::OPTION_FILESYSTEM_PREFIX => 'a://',
        ]));
    }

    public function testFilesystemShouldBeCleaned(): void
    {
        $cleaner = new StorageCleaner(new DefaultFileKeepResolver());
        $filesystem = $this->createFilesystem(self::FS_A_FILES);

        Assert::same(8, $this->countFiles($filesystem));

        $cleaner->clean($filesystem);

        Assert::same(1, $this->countFiles($filesystem));
        Assert::true($filesystem->fileExists('.gitkeep'));
    }

    public function testFilesystemNamespaceShouldBeCleaned(): void
    {
        $cleaner = new StorageCleaner(new DefaultFileKeepResolver());
        $filesystem = $this->createFilesystem(self::FS_A_FILES);

        Assert::same(8, $this->countFiles($filesystem));

        $cleaner->clean($filesystem, [
            StorageCleanerInterface::OPTION_NAMESPACE => 'var/nette',
        ]);

        Assert::same(5, $this->countFiles($filesystem));
        Assert::false($filesystem->fileExists('var/nette/configurator/presenters.php'));
        Assert::false($filesystem->fileExists('var/nette/container/ContainerA.php'));
        Assert::false($filesystem->fileExists('var/nette/container/ContainerB.php'));
    }

    public function testFilesystemNamespaceShouldBeCleanedOnMount(): void
    {
        $cleaner = new StorageCleaner(new DefaultFileKeepResolver());
        $filesystem = new MountManager([
            'a' => $this->createFilesystem(self::FS_A_FILES),
            'b' => $this->createFilesystem(self::FS_B_FILES),
        ]);

        Assert::same(8, $this->countFiles($filesystem, 'a://'));

        $cleaner->clean($filesystem, [
            StorageCleanerInterface::OPTION_NAMESPACE => 'var/nette/container',
            StorageCleanerInterface::OPTION_FILESYSTEM_PREFIX => 'a://',
        ]);

        Assert::same(6, $this->countFiles($filesystem, 'a://'));
        Assert::false($filesystem->fileExists('a://var/nette/container/ContainerA.php'));
        Assert::false($filesystem->fileExists('a://var/nette/container/ContainerB.php'));
    }

    private function createFilesystem(array $files): Filesystem
    {
        $fs = new Filesystem(
            new InMemoryFilesystemAdapter(),
        );

        foreach ($files as $filename => $content) {
            $fs->write($filename, $content);
        }

        return $fs;
    }

    private function countFiles(FilesystemOperator $filesystem, string $location = ''): int
    {
        return count(
            $filesystem
                ->listContents($location, FilesystemReader::LIST_DEEP)
                ->filter(static fn (StorageAttributes $attributes): bool => $attributes->isFile())
                ->toArray(),
        );
    }
}

(new StorageCleanerTest())->run();
