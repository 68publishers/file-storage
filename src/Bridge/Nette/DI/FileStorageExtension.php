<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Nette\DI;

use ReflectionClass;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use ReflectionException;
use Nette\DI\CompilerExtension;
use League\Flysystem\Filesystem;
use League\Flysystem\Visibility;
use Composer\Autoload\ClassLoader;
use Nette\DI\Definitions\Statement;
use Nette\DI\Definitions\Definition;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Config as FlysystemConfig;
use SixtyEightPublishers\FileStorage\FileStorage;
use League\Flysystem\Local\LocalFilesystemAdapter;
use SixtyEightPublishers\FileStorage\Config\Config;
use SixtyEightPublishers\FileStorage\Asset\AssetFactory;
use SixtyEightPublishers\FileStorage\Asset\AssetsCopier;
use SixtyEightPublishers\FileStorage\Asset\PathsProvider;
use SixtyEightPublishers\FileStorage\FileStorageProvider;
use SixtyEightPublishers\FileStorage\FileStorageInterface;
use SixtyEightPublishers\FileStorage\Cleaner\StorageCleaner;
use SixtyEightPublishers\FileStorage\Config\ConfigInterface;
use SixtyEightPublishers\FileStorage\Resource\ResourceFactory;
use SixtyEightPublishers\FileStorage\Persistence\FilePersister;
use SixtyEightPublishers\FileStorage\Exception\RuntimeException;
use SixtyEightPublishers\FileStorage\Asset\AssetFactoryInterface;
use SixtyEightPublishers\FileStorage\Asset\AssetsCopierInterface;
use SixtyEightPublishers\FileStorage\LinkGenerator\LinkGenerator;
use SixtyEightPublishers\FileStorage\Asset\PathsProviderInterface;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;
use SixtyEightPublishers\FileStorage\Cleaner\DefaultFileKeepResolver;
use SixtyEightPublishers\FileStorage\Cleaner\StorageCleanerInterface;
use SixtyEightPublishers\FileStorage\Cleaner\FileKeepResolverInterface;
use SixtyEightPublishers\FileStorage\Resource\ResourceFactoryInterface;
use SixtyEightPublishers\FileStorage\Persistence\FilePersisterInterface;
use SixtyEightPublishers\FileStorage\Bridge\Nette\DI\Config\StorageConfig;
use SixtyEightPublishers\FileStorage\LinkGenerator\LinkGeneratorInterface;
use SixtyEightPublishers\FileStorage\Bridge\Nette\DI\Config\FilesystemConfig;
use SixtyEightPublishers\FileStorage\Bridge\Nette\DI\Config\FileStorageConfig;
use function count;
use function assert;
use function dirname;
use function sprintf;
use function array_filter;
use function class_exists;

final class FileStorageExtension extends CompilerExtension implements FileStorageDefinitionFactoryInterface
{
	private string $rootDir;

	public function __construct(?string $rootDir = null)
	{
		$this->rootDir = $rootDir ?? $this->guessRootDir();
	}

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'storages' => Expect::arrayOf(
				Expect::structure([ # the first one storage is default
					'config' => Expect::array([]),
					'filesystem' => Expect::structure([
						'adapter' => Expect::anyOf(Expect::string(), Expect::type(Statement::class))->required()->before(static function ($factory) {
							return $factory instanceof Statement ? $factory : new Statement($factory);
						}),
						'config' => Expect::array([
							FlysystemConfig::OPTION_VISIBILITY => Visibility::PUBLIC,
							FlysystemConfig::OPTION_DIRECTORY_VISIBILITY => Visibility::PUBLIC,
						])->mergeDefaults(true),
					])->castTo(FilesystemConfig::class),
					'assets' => Expect::arrayOf('string', 'string'),
				])->castTo(StorageConfig::class)
			),
		])->assert(static function (object $config) {
			return isset($config->storages) && 0 < count($config->storages);
		})->castTo(FileStorageConfig::class);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig();
		assert($config instanceof FileStorageConfig);

		$factories = array_filter($this->compiler->getExtensions(FileStorageDefinitionFactoryInterface::class), function ($extension) {
			return $extension !== $this;
		});

		$factories[] = $this;
		$storageDefinitions = [];
		$defaultStorageDefinition = null;

		foreach ($config->storages as $storageName => $storageConfig) {
			foreach ($factories as $factory) {
				assert($factory instanceof FileStorageDefinitionFactoryInterface);

				if (!$factory->canCreateFileStorage($storageName, $storageConfig)) {
					continue;
				}

				$storageDefinition = $factory->createFileStorage($storageName, $storageConfig);

				if (null === $defaultStorageDefinition) {
					$defaultStorageDefinition = $storageDefinition->setAutowired(true);
				} else {
					$storageDefinitions[] = $storageDefinition->setAutowired(false);
				}

				continue 2;
			}
		}

		$builder->addDefinition($this->prefix('file_storage_provider'))
			->setType(FileStorageProviderInterface::class)
			->setFactory(FileStorageProvider::class, [
				$defaultStorageDefinition,
				$storageDefinitions,
			]);

		$this->registerStorageCleaner();
		$this->registerAssets($config);
	}

	public function canCreateFileStorage(string $name, object $config): bool
	{
		return true;
	}

	public function createFileStorage(string $name, StorageConfig $config): Definition
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('filesystem.' . $name))
			->setType(FilesystemOperator::class)
			->setFactory(Filesystem::class, [
				$config->filesystem->adapter,
				$config->filesystem->config,
			])
			->setAutowired(false);

		$builder->addDefinition($this->prefix('config.' . $name))
			->setType(ConfigInterface::class)
			->setFactory(Config::class, [$config->config])
			->setAutowired(false);

		$builder->addDefinition($this->prefix('resource_factory.' . $name))
			->setType(ResourceFactoryInterface::class)
			->setFactory(ResourceFactory::class, [$this->prefix('@filesystem.' . $name)])
			->setAutowired(false);

		$builder->addDefinition($this->prefix('link_generator.' . $name))
			->setType(LinkGeneratorInterface::class)
			->setFactory(LinkGenerator::class, [$this->prefix('@config.' . $name)])
			->setAutowired(false);

		$builder->addDefinition($this->prefix('file_persister.' . $name))
			->setType(FilePersisterInterface::class)
			->setFactory(FilePersister::class, [$this->prefix('@filesystem.' . $name)])
			->setAutowired(false);

		return $builder->addDefinition($this->prefix('file_storage.' . $name))
			->setType(FileStorageInterface::class)
			->setFactory(FileStorage::class, [
				$name,
				$this->prefix('@config.' . $name),
				$this->prefix('@resource_factory.' . $name),
				$this->prefix('@link_generator.' . $name),
				$this->prefix('@file_persister.' . $name),
			])
			->setAutowired(false);
	}

	private function registerStorageCleaner(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('file_keep_resolver'))
			->setType(FileKeepResolverInterface::class)
			->setFactory(DefaultFileKeepResolver::class);

		$builder->addDefinition($this->prefix('storage_cleaner'))
			->setType(StorageCleanerInterface::class)
			->setFactory(StorageCleaner::class);
	}

	private function registerAssets(FileStorageConfig $config): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('assets.local_filesystem'))
			->setType(FilesystemOperator::class)
			->setFactory(Filesystem::class, [
				new Statement(LocalFilesystemAdapter::class, [$this->rootDir]),
			])
			->setAutowired(false);

		$builder->addDefinition($this->prefix('assets.asset_factory'))
			->setType(AssetFactoryInterface::class)
			->setFactory(AssetFactory::class)
			->setAutowired(false);

		$provider = $builder->addDefinition($this->prefix('assets.paths_provider'))
			->setType(PathsProviderInterface::class)
			->setFactory(PathsProvider::class)
			->setAutowired(false);

		$builder->addDefinition($this->prefix('assets.assets_copier'))
			->setType(AssetsCopierInterface::class)
			->setFactory(AssetsCopier::class, [
				$this->prefix('@assets.local_filesystem'),
				$this->prefix('@assets.paths_provider'),
				$this->prefix('@assets.asset_factory'),
			]);

		foreach ($config->storages as $storageName => $storageConfig) {
			if (!empty($storageConfig->assets)) {
				$provider->addSetup('addPaths', [$storageName, $storageConfig->assets]);
			}
		}

		foreach ($this->compiler->getExtensions(AssetsProviderInterface::class) as $assetsProviderExtension) {
			assert($assetsProviderExtension instanceof AssetsProviderInterface);

			foreach ($assetsProviderExtension->provideAssets() as $assets) {
				if (!empty($assets->paths)) {
					$provider->addSetup('addPaths', [$assets->storageName, $assets->paths]);
				}
			}
		}
	}

	/**
	 * @throws \SixtyEightPublishers\FileStorage\Exception\RuntimeException
	 */
	private function guessRootDir(): string
	{
		if (!class_exists(ClassLoader::class)) {
			throw new RuntimeException(sprintf(
				'Project root directory can\'t be detected because the class %s can\'t be found. Please provide the root directory manually into the %s::__construct().',
				ClassLoader::class,
				self::class
			));
		}

		try {
			$reflection = new ReflectionClass(ClassLoader::class);

			return dirname((string) $reflection->getFileName(), 3);
		} catch (ReflectionException $e) {
			throw new RuntimeException(sprintf(
				'Project root directory can\'t be detected. Please provide the root directory manually  into the %s::__construct().',
				self::class
			), 0, $e);
		}
	}
}
