<h1 align="center">File Storage</h1>

<p align="center">:file_folder: File management based on <a href="https://github.com/thephpleague/flysystem">Flysystem</a> with an integration into <a href="https://nette.org">Nette Framework</a>.</p>

<p align="center">
<a href="https://github.com/68publishers/file-storage/actions"><img alt="Checks" src="https://badgen.net/github/checks/68publishers/file-storage/master"></a>
<a href="https://coveralls.io/github/68publishers/file-storage?branch=master"><img alt="Coverage Status" src="https://coveralls.io/repos/github/68publishers/file-storage/badge.svg?branch=master"></a>
<a href="https://packagist.org/packages/68publishers/file-storage"><img alt="Total Downloads" src="https://badgen.net/packagist/dt/68publishers/file-storage"></a>
<a href="https://packagist.org/packages/68publishers/file-storage"><img alt="Latest Version" src="https://badgen.net/packagist/v/68publishers/file-storage"></a>
<a href="https://packagist.org/packages/68publishers/file-storage"><img alt="PHP Version" src="https://badgen.net/packagist/php/68publishers/file-storage"></a>
</p>

## Installation

The best way to install 68publishers/file-storage is using Composer:

```sh
$ composer require 68publishers/file-storage
```

## Integration into Nette Framework

With this extension, you can register more storages with different roots, filesystem adapters etc.
The first registered storage is also considered as the default storage.

### Configuration example

```neon
extensions:
    68publishers.file_storage: SixtyEightPublishers\FileStorage\Bridge\Nette\DI\FileStorageExtension

68publishers.file_storage:
    storages:
        default:
            config:
                base_path: /data/files
            filesystem:
                adapter: League\Flysystem\Local\LocalFilesystemAdapter(%wwwDir%/data/files)
                config: # an optional config for filesystem adapter
            assets:
                path/to/file.png: my/file.png # single file copying
                path/to/directory: my-directory # copy whole directory
        s3:
            config:
                host: https://my-buket.s3.amazonaws.com
            filesystem:
                adapter: League\Flysystem\AwsS3V3\AwsS3V3Adapter(@s3client, my-bucket)
```

#### Storage config options

| name                   | type           | default | description                                                                                     |
|------------------------|----------------|---------|-------------------------------------------------------------------------------------------------|
| base_path              | string         | `''`    | Base path to a directory where the files are accessible.                                        |
| host                   | null or string | `null`  | Hostname, use if the files are not stored locally or if you want to generate an absolute links. |
| version_parameter_name | `_v`           | default | Name of a version parameter in URL.                                                             |

### Basic usage

Generated DI Container will contain an autowired services of type `FileStorageProviderInterface` and `FileStorageInterface` (the default storage).

```php
<?php

use SixtyEightPublishers\FileStorage\FileStorageInterface;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;

/** @var \Nette\DI\Container $container */

$defaultStorage = $container->getByType(FileStorageInterface::class);

$provider = $container->getByType(FileStorageProviderInterface::class);

$defaultStorage = $provider->get();
# or $defaultStorage = $provider->get('default');
$s3storage = $provider->get('s3');

```

#### Persisting files

```php
<?php

/** @var \SixtyEightPublishers\FileStorage\FileStorageInterface $storage */

# Create resource from local file:
$resource = $storage->createResourceFromLocalFile(
    $storage->createPathInfo('test/invoice.pdf'),
    __DIR__ . '/path/to/invoice.pdf'
);

$storage->save($resource);

# Create resource from file that is stored in storage:
$resource = $storage->createResource(
    $storage->createPathInfo('test/invoice.pdf')
);

# copy to the new location
$storage->save($resource->withPathInfo(
    $storage->createPathInfo('test/invoice-2.pdf')
));
```

#### Check a file existence

```php
<?php

/** @var \SixtyEightPublishers\FileStorage\FileStorageInterface $storage */

if ($storage->exists($storage->createPathInfo('test/invoice.pdf'))) {
    echo 'file exists!';
}
```

#### Deleting files

```php
<?php

/** @var \SixtyEightPublishers\FileStorage\FileStorageInterface $storage */

$storage->delete($storage->createPathInfo('test/invoice.pdf'));
```

#### Create links to files

```php
<?php

/** @var \SixtyEightPublishers\FileStorage\FileStorageInterface $storage */

# /data/files/test/invoice.pdf
echo $storage->link($storage->createPathInfo('test/invoice.pdf'));

# or

$fileInfo = $storage->createFileInfo($storage->createPathInfo('test/invoice.pdf'));

echo $fileInfo->link();
```

#### Cleaning the storage

```php
<?php

use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;
use SixtyEightPublishers\FileStorage\Cleaner\StorageCleanerInterface;

/** @var \Nette\DI\Container $container */

$cleaner = $container->getByType(StorageCleanerInterface::class);
$provider = $container->getByType(FileStorageProviderInterface::class);
$storage = $provider->get('default');

# get files count in specific namespace:
$cleaner->getCount($storage->getFilesystem(), [
    StorageCleanerInterface::OPTION_NAMESPACE => 'test',
]);

# get files count in whole storage:
$cleaner->getCount($storage->getFilesystem());

# remove files in specific namespace:
$cleaner->clean($storage->getFilesystem(), [
    StorageCleanerInterface::OPTION_NAMESPACE => 'test',
]);

# clean whole storage:
$cleaner->clean($storage->getFilesystem());
```

#### Assets copying

```php
<?php

use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;
use SixtyEightPublishers\FileStorage\Asset\AssetsCopierInterface;

/** @var \Nette\DI\Container $container */

$copier = $container->getByType(AssetsCopierInterface::class);
$provider = $container->getByType(FileStorageProviderInterface::class);

# Copies assets defined in the configuration
$copier->copy($provider->get('default'));
$copier->copy($provider->get('s3'));
```

Assets can be defined in the configuration under each storage separately but compiler extensions can define other assets:

```php
<?php

use Nette\DI\CompilerExtension;
use SixtyEightPublishers\FileStorage\Bridge\Nette\DI\Assets;
use SixtyEightPublishers\FileStorage\Bridge\Nette\DI\AssetsProviderInterface;

final class MyCompilerExtension extends CompilerExtension implements AssetsProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function provideAssets() : array
    {
        return [
            new Assets('s3', [
                'path/to/file1.jpeg' => 'namespace/file1.jpeg',
                'path/to/file2.jpeg' => 'namespace/file2.jpeg',
            ]),
        ];
    }
}
```

### Usage with Doctrine ORM

The package provides custom Doctrine DBAL type `file_info`. You can register it manually in this way:

```php
<?php

use Doctrine\DBAL\Types\Type;
use SixtyEightPublishers\FileStorage\Bridge\Doctrine\DbalType\FileInfo\FileInfoType;

/** @var \SixtyEightPublishers\FileStorage\FileStorageProviderInterface $fileStorageProvider */

Type::addType(FileInfoType::NAME, FileInfoType::class);

# this line is important:
Type::getType(FileInfoType::NAME)->setFileStorageProvider($fileStorageProvider);
```

Or you can use a compiler extension `FileStorageDoctrineExtension` but the extension requires an integration of package [68publishers/doctrine-bridge](https://github.com/68publishers/doctrine-bridge).

```neon
extensions:
    68publishers.file_storage.doctrine: SixtyEightPublishers\FileStorage\Bridge\Nette\DI\FileStorageDoctrineExtension

68publishers.file_storage.doctrine:
    type_name: file_info # default
```

#### Example entity and persistence

```php
<?php

use Doctrine\ORM\Mapping as ORM;
use SixtyEightPublishers\FileStorage\FileInfoInterface;

/**
 * @ORM\Entity
 */
class File
{
    # ID and other columns

    /**
     * @ORM\Column(type="file_info")
     *
     * @var \SixtyEightPublishers\FileStorage\FileInfoInterface
     */
    protected $source;

    public function __construct(FileInfoInterface $source)
    {
        $this->source = $source;
    }

    public function getSource(): FileInfoInterface
    {
        return $this->source;
    }
}
```

```php
/** @var Doctrine\ORM\EntityManagerInterface $em */
/** @var \SixtyEightPublishers\FileStorage\FileStorageInterface $storage */

$pathInfo = $storage->createPathInfo('test/avatar.png');
$resource = $storage->createResourceFromLocalFile($pathInfo, __DIR__ . '/path/to/uploaded/file.png');

$storage->save($resource);

$pathInfo->setVersion(time());
$entity = new File($storage->createFileInfo($pathInfo));

$em->persist($entity);
$em->flush();

# /data/files/test/avatar.png?_v=1611837352
echo (string) $entity->getSource();
```

### Usage with Latte

```neon
extensions:
    68publishers.file_storage.latte: SixtyEightPublishers\FileStorage\Bridge\Nette\DI\FileStorageLatteExtension
```

```latte
{varType SixtyEightPublishers\FileStorage\FileInfoInterface $fileInfo}

{* method FileInfo::__toString() calls ::link() internally so both lines are the same: *}
<a href="{$fileInfo->link()}" download>Download a file</a>
<a href="{$fileInfo}" download>Download a file</a>

{* Create FileInfo from string *}
<a href="{file_info('test/invoice.pdf')}" download>Download a file</a>
```

### Symfony Console commands

```neon
extensions:
    68publishers.file_storage.console: SixtyEightPublishers\FileStorage\Bridge\Nette\DI\FileStorageConsoleExtension
```

Clean storage command:

```sh
$ bin/console file-storage:clean [<storage>] [--namespace <value>]
```

Copy storage assets:

```sh
$ bin/console file-storage:copy-assets [<storage>]
```

## Contributing

Before opening a pull request, please check your changes using the following commands

```sh
$ make init # to pull and start all docker images

$ make cs.check
$ make stan
$ make tests.all
```
