<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use SixtyEightPublishers\FileStorage\Asset\AssetsCopierInterface;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;

final class CopyAssetsCommand extends Command
{
	/** @var \SixtyEightPublishers\FileStorage\Asset\AssetsCopierInterface  */
	private $assetsCopier;

	/** @var \SixtyEightPublishers\FileStorage\FileStorageProviderInterface  */
	private $fileStorageProvider;

	/**
	 * @param \SixtyEightPublishers\FileStorage\Asset\AssetsCopierInterface  $assetsCopier
	 * @param \SixtyEightPublishers\FileStorage\FileStorageProviderInterface $fileStorageProvider
	 */
	public function __construct(AssetsCopierInterface $assetsCopier, FileStorageProviderInterface $fileStorageProvider)
	{
		parent::__construct();

		$this->assetsCopier = $assetsCopier;
		$this->fileStorageProvider = $fileStorageProvider;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function configure(): void
	{
		$this->setName('file-storage:copy-assets')
			->setDescription('Copies assets from a defined paths to a configured storage.')
			->addArgument('storage', InputArgument::OPTIONAL, 'Copy assets for specific storage only.', NULL);
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute(InputInterface $input, OutputInterface $output): int
	{
		$storageName = $input->getArgument('storage');
		$logger = new ConsoleLogger($output);

		/** @var \SixtyEightPublishers\FileStorage\FileStorageProviderInterface $storage */
		foreach (NULL !== $storageName ? [$this->fileStorageProvider->get($storageName)] : iterator_to_array($this->fileStorageProvider) as $storage) {
			$this->assetsCopier->copy($storage, $logger);
		}

		return 0;
	}
}
