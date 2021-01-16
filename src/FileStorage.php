<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage;

use League\Flysystem\FilesystemOperator;
use SixtyEightPublishers\FileStorage\Helper\Path;
use SixtyEightPublishers\FileStorage\Config\ConfigInterface;
use SixtyEightPublishers\FileStorage\Resource\ResourceInterface;
use SixtyEightPublishers\FileStorage\Resource\ResourceFactoryInterface;
use SixtyEightPublishers\FileStorage\Persistence\FilePersisterInterface;
use SixtyEightPublishers\FileStorage\LinkGenerator\LinkGeneratorInterface;

class FileStorage implements FileStorageInterface
{
	/** @var string  */
	protected $name;

	/** @var \SixtyEightPublishers\FileStorage\Config\ConfigInterface  */
	protected $config;

	/** @var \SixtyEightPublishers\FileStorage\Resource\ResourceFactoryInterface  */
	protected $resourceFactory;

	/** @var \SixtyEightPublishers\FileStorage\LinkGenerator\LinkGeneratorInterface  */
	protected $linkGenerator;

	/** @var \SixtyEightPublishers\FileStorage\Persistence\FilePersisterInterface  */
	protected $filePersister;

	/**
	 * @param string                                                                 $name
	 * @param \SixtyEightPublishers\FileStorage\Config\ConfigInterface               $config
	 * @param \SixtyEightPublishers\FileStorage\Resource\ResourceFactoryInterface    $resourceFactory
	 * @param \SixtyEightPublishers\FileStorage\LinkGenerator\LinkGeneratorInterface $linkGenerator
	 * @param \SixtyEightPublishers\FileStorage\Persistence\FilePersisterInterface   $filePersister
	 */
	public function __construct(string $name, ConfigInterface $config, ResourceFactoryInterface $resourceFactory, LinkGeneratorInterface $linkGenerator, FilePersisterInterface $filePersister)
	{
		$this->name = $name;
		$this->config = $config;
		$this->resourceFactory = $resourceFactory;
		$this->linkGenerator = $linkGenerator;
		$this->filePersister = $filePersister;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFilesystem(): FilesystemOperator
	{
		return $this->filePersister->getFilesystem();
	}

	/**
	 * {@inheritDoc}
	 */
	public function exists(PathInfoInterface $pathInfo): bool
	{
		return $this->filePersister->exists($pathInfo);
	}

	/**
	 * {@inheritDoc}
	 */
	public function save(ResourceInterface $resource, array $config = []): string
	{
		return $this->filePersister->save($resource, $config);
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete(PathInfoInterface $pathInfo, array $config = []): void
	{
		$this->filePersister->delete($pathInfo, $config);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getConfig(): ConfigInterface
	{
		return $this->config;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \SixtyEightPublishers\FileStorage\Exception\PathInfoException
	 */
	public function createPathInfo(string $path): PathInfoInterface
	{
		return new PathInfo(...Path::parse($path));
	}

	/**
	 * {@inheritDoc}
	 */
	public function createFileInfo(PathInfoInterface $pathInfo): FileInfoInterface
	{
		return new FileInfo($this->linkGenerator, $pathInfo, $this->getName());
	}

	/**
	 * {@inheritDoc}
	 */
	public function link(PathInfoInterface $pathInfo): string
	{
		return $this->linkGenerator->link($pathInfo);
	}

	/**
	 * {@inheritDoc}
	 */
	public function createResource(PathInfoInterface $pathInfo): ResourceInterface
	{
		return $this->resourceFactory->createResource($pathInfo);
	}

	/**
	 * {@inheritDoc}
	 */
	public function createResourceFromLocalFile(PathInfoInterface $pathInfo, string $filename): ResourceInterface
	{
		return $this->resourceFactory->createResourceFromLocalFile($pathInfo, $filename);
	}
}
