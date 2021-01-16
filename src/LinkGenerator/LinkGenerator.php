<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\LinkGenerator;

use SixtyEightPublishers\FileStorage\PathInfoInterface;
use SixtyEightPublishers\FileStorage\Config\ConfigInterface;

class LinkGenerator implements LinkGeneratorInterface
{
	/** @var \SixtyEightPublishers\FileStorage\Config\ConfigInterface  */
	private $config;

	/**
	 * @param \SixtyEightPublishers\FileStorage\Config\ConfigInterface $config
	 */
	public function __construct(ConfigInterface $config)
	{
		$this->config = $config;
	}

	/**
	 * {@inheritdoc}
	 */
	public function link(PathInfoInterface $pathInfo): string
	{
		$basePath = $this->config[ConfigInterface::BASE_PATH];
		$path = $pathInfo->getPath();
		$link = (!empty($basePath) ? '/' : '') . $basePath . '/' . $path;
		$queryParameters = $this->buildQueryParams($pathInfo);

		foreach ($queryParameters as $k => $v) {
			$queryParameters[$k] = empty($k) ? $v : $k . '=' . $v;
		}

		if (!empty($queryParameters)) {
			$link .= '?' . implode('&', $queryParameters);
		}

		if (!empty($this->config[ConfigInterface::HOST])) {
			$link = $this->config[ConfigInterface::HOST] . $link;
		}

		return rawurldecode($link);
	}

	/**
	 * @param \SixtyEightPublishers\FileStorage\PathInfoInterface $pathInfo
	 *
	 * @return array
	 */
	protected function buildQueryParams(PathInfoInterface $pathInfo): array
	{
		$params = [];

		if (NULL !== $pathInfo->getVersion()) {
			$versionParameterName = $this->config[ConfigInterface::VERSION_PARAMETER_NAME];
			$params[$versionParameterName] = $pathInfo->getVersion();
		}

		return $params;
	}
}
