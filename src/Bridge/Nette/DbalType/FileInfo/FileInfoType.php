<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Nette\DbalType\FileInfo;

use Nette\DI\Container;
use SixtyEightPublishers\FileStorage\FileStorageProviderInterface;
use SixtyEightPublishers\DoctrineBridge\Type\ContainerAwareTypeInterface;
use SixtyEightPublishers\FileStorage\Bridge\Doctrine\DbalType\FileInfo\FileInfoType as DoctrineFileInfoType;

final class FileInfoType extends DoctrineFileInfoType implements ContainerAwareTypeInterface
{
	public function setContainer(Container $container, array $context = []): void
	{
		$this->setFileStorageProvider(
			$container->getByType(FileStorageProviderInterface::class)
		);
	}
}
