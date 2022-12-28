<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use SixtyEightPublishers\FileStorage\FileStorageInterface;
use SixtyEightPublishers\FileStorage\Asset\AssetsCopierInterface;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;
use function assert;
use function iterator_to_array;

final class CopyAssetsCommand extends Command
{
	public function __construct(
		private readonly AssetsCopierInterface $assetsCopier,
		private readonly FileStorageProviderInterface $fileStorageProvider,
	) {
		parent::__construct();
	}

	protected function configure(): void
	{
		$this->setName('file-storage:copy-assets')
			->setDescription('Copies assets from a defined paths to a configured storage.')
			->addArgument('storage', InputArgument::OPTIONAL, 'Copy assets for specific storage only.');
	}

	public function execute(InputInterface $input, OutputInterface $output): int
	{
		$storageName = $input->getArgument('storage');
		$logger = new ConsoleLogger($output);

		foreach (null !== $storageName ? [$this->fileStorageProvider->get($storageName)] : iterator_to_array($this->fileStorageProvider) as $storage) {
			assert($storage instanceof FileStorageInterface);

			$this->assetsCopier->copy($storage, $logger);
		}

		return 0;
	}
}
