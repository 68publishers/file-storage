<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\OutputInterface;
use SixtyEightPublishers\FileStorage\FileStorageInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;
use SixtyEightPublishers\FileStorage\Cleaner\StorageCleanerInterface;
use SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Configurator\CleanCommandConfiguratorInterface;
use function assert;
use function implode;
use function array_map;
use function array_sum;
use function array_keys;
use function iterator_to_array;

class CleanCommand extends Command
{
	public function __construct(
		private readonly FileStorageProviderInterface $fileStorageProvider,
		private readonly StorageCleanerInterface $storageCleaner,
		private readonly CleanCommandConfiguratorInterface $cleanCommandConfigurator,
	) {
		parent::__construct();
	}

	protected function configure(): void
	{
		$this->setName('file-storage:clean')
			->setDescription('Cleans file storage')
			->addArgument('storage', InputArgument::OPTIONAL, 'Clean only specific storage.', null);

		$this->cleanCommandConfigurator->setupOptions($this);
	}

	public function execute(InputInterface $input, OutputInterface $output): int
	{
		$storageName = $input->getArgument('storage');
		$cleanerOptions = $this->cleanCommandConfigurator->getCleanerOptions($input);

		if (null !== $storageName) {
			$storage = $this->fileStorageProvider->get($storageName);
			$filesystem = $storage->getFilesystem();
			$deleteCount = $this->storageCleaner->getCount($filesystem, $cleanerOptions);

			if ($this->ask($input, $output, $deleteCount, '"' . $storageName . '"')) {
				$this->storageCleaner->clean($filesystem, $cleanerOptions);
				$output->writeln(\sprintf('Storage "%s" has been successfully cleaned.', $storageName));
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
			$output->writeln(\sprintf('Storage "%s" has been successfully cleaned.', $storage->getName()));
		}

		return 0;
	}

	private function ask(InputInterface $input, OutputInterface $output, int $count, string $storageName): bool
	{
		$helper = $this->getHelper('question');
		assert($helper instanceof QuestionHelper);

		return (bool) $helper->ask($input, $output, new ConfirmationQuestion(sprintf(
			'Do you want to delete %d files in a storage %s? ',
			$count,
			$storageName
		), false));
	}
}
