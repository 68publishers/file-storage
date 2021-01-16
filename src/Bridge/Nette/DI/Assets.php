<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Bridge\Nette\DI;

final class Assets
{
	/** @var string  */
	public $storageName;

	/** @var array  */
	public $paths;

	/**
	 * @param string $storageName
	 * @param array  $paths
	 */
	public function __construct(string $storageName, array $paths)
	{
		$this->storageName = $storageName;
		$this->paths = $paths;
	}
}
