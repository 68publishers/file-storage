<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Symfony\Console\Command;

use SixtyEightPublishers\FileStorage\Asset\AssetsCopierInterface;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use function is_string;
use function iterator_to_array;

#[AsCommand(name: 'file-storage:copy-assets', description: 'Copies assets from a defined paths to a configured storage.')]
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
        $this->addArgument('storage', InputArgument::OPTIONAL, 'Copy assets for specific storage only.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $storageName = $input->getArgument('storage');
        $logger = new ConsoleLogger($output);

        foreach (is_string($storageName) ? [$this->fileStorageProvider->get($storageName)] : iterator_to_array($this->fileStorageProvider) as $storage) {
            $this->assetsCopier->copy($storage, $logger);
        }

        return 0;
    }
}
