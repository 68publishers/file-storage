<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SixtyEightPublishers\FileStorage\FileStorageInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;
use SixtyEightPublishers\FileStorage\Cleaner\StorageCleanerInterface;
use SixtyEightPublishers\FileStorage\Bridge\Console\Configurator\CleanCommandConfiguratorInterface;

class CleanCommand extends Command
{
	/** @var \SixtyEightPublishers\FileStorage\FileStorageProviderInterface  */
	private $fileStorageProvider;

	/** @var \SixtyEightPublishers\FileStorage\Cleaner\StorageCleanerInterface  */
	private $storageCleaner;

	/** @var \SixtyEightPublishers\FileStorage\Bridge\Console\Configurator\CleanCommandConfiguratorInterface  */
	private $cleanCommandConfigurator;

	/**
	 * @param \SixtyEightPublishers\FileStorage\FileStorageProviderInterface                                  $fileStorageProvider
	 * @param \SixtyEightPublishers\FileStorage\Cleaner\StorageCleanerInterface                               $storageCleaner
	 * @param \SixtyEightPublishers\FileStorage\Bridge\Console\Configurator\CleanCommandConfiguratorInterface $cleanCommandConfigurator
	 */
	public function __construct(FileStorageProviderInterface $fileStorageProvider, StorageCleanerInterface $storageCleaner, CleanCommandConfiguratorInterface $cleanCommandConfigurator)
	{
		$this->fileStorageProvider = $fileStorageProvider;
		$this->storageCleaner = $storageCleaner;
		$this->cleanCommandConfigurator = $cleanCommandConfigurator;

		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function configure(): void
	{
		$this->setName('file-storage:clean')
			->setDescription('Cleans file storage')
			->addArgument('storage', InputArgument::OPTIONAL, 'Clean only specific storage.', NULL);

		$this->cleanCommandConfigurator->setupOptions($this);
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute(InputInterface $input, OutputInterface $output): int
	{
		$storageName = $input->getArgument('storage');
		$cleanerOptions = $this->cleanCommandConfigurator->getCleanerOptions($input);

		if (NULL !== $storageName) {
			$storage = $this->fileStorageProvider->get($storageName);
			$filesystem = $storage->getFilesystem();
			$deleteCount = $this->storageCleaner->getCount($filesystem, $cleanerOptions);

			if ($this->ask($input, $output, $deleteCount, '"' . $storageName . '"')) {
				$this->storageCleaner->clean($filesystem, $cleanerOptions);
				$output->writeln(sprintf('Storage %s was successfully cleaned.', $storageName));
			}

			return 0;
		}

		$storages = iterator_to_array($this->fileStorageProvider);
		$deleteCount = array_sum(array_map(function (FileStorageInterface $fileStorage) use ($cleanerOptions) {
			return $this->storageCleaner->getCount($fileStorage->getFilesystem(), $cleanerOptions);
		}, $storages));

		if (!$this->ask($input, $output, $deleteCount, '"' . implode('", "', array_keys($storages)) . '"')) {
			return 0;
		}

		foreach ($storages as $storage) {
			$this->storageCleaner->clean($storage->getFilesystem(), $cleanerOptions);
			$output->writeln(sprintf('Storage %s was successfully cleaned.', $storage->getName()));
		}

		return 0;
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface   $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 * @param int                                               $count
	 * @param string                                            $storageName
	 *
	 * @return bool
	 */
	private function ask(InputInterface $input, OutputInterface $output, int $count, string $storageName): bool
	{
		return (bool) $this->getHelper('question')->ask($input, $output, new ConfirmationQuestion(sprintf(
			'Do you want to delete %d files in a storage %s? ',
			$count,
			$storageName
		), FALSE));
	}
}
