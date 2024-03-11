<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\LinkGenerator;

use SixtyEightPublishers\FileStorage\Config\ConfigInterface;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use function implode;
use function is_string;
use function rawurldecode;

class LinkGenerator implements LinkGeneratorInterface
{
    public function __construct(
        private readonly ConfigInterface $config,
    ) {}

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
     * @return array<string, string>
     */
    protected function buildQueryParams(PathInfoInterface $pathInfo): array
    {
        $params = [];

        if (null !== $pathInfo->getVersion()) {
            $versionParameterName = $this->config[ConfigInterface::VERSION_PARAMETER_NAME];
            $params[is_string($versionParameterName) ? $versionParameterName : ''] = $pathInfo->getVersion();
        }

        return $params;
    }
}
