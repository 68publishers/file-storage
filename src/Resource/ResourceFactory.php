<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Resource;

use League\Flysystem\FilesystemException as LeagueFilesystemException;
use League\Flysystem\FilesystemReader;
use League\Flysystem\UnableToRetrieveMetadata;
use SixtyEightPublishers\FileStorage\Exception\FileNotFoundException;
use SixtyEightPublishers\FileStorage\Exception\FilesystemException;
use SixtyEightPublishers\FileStorage\PathInfoInterface;
use function array_shift;
use function error_clear_last;
use function error_get_last;
use function explode;
use function file_exists;
use function filter_var;
use function fopen;
use function is_file;
use function sprintf;
use function str_starts_with;
use function stream_context_create;
use function stream_get_meta_data;
use function strlen;
use function strtolower;
use function trim;

final class ResourceFactory implements ResourceFactoryInterface
{
    public function __construct(
        private readonly FilesystemReader $filesystemReader,
    ) {}

    /**
     * @throws FileNotFoundException
     * @throws LeagueFilesystemException
     * @throws FilesystemException
     */
    public function createResource(PathInfoInterface $pathInfo): ResourceInterface
    {
        $path = $pathInfo->getPath();

        if (false === $this->filesystemReader->fileExists($path)) {
            throw new FileNotFoundException($path);
        }

        try {
            $source = $this->filesystemReader->readStream($path);
        } catch (LeagueFilesystemException $e) {
            throw new FilesystemException(
                message: sprintf(
                    'Can not read stream from file "%s".',
                    $path,
                ),
                previous: $e,
            );
        }

        return new StreamResource(
            pathInfo: $pathInfo,
            source: $source,
            mimeType: function () use ($path): ?string {
                try {
                    return $this->filesystemReader->mimeType($path);
                } catch (LeagueFilesystemException|UnableToRetrieveMetadata $e) {
                    return null;
                }
            },
            filesize: function () use ($path): ?int {
                try {
                    return $this->filesystemReader->fileSize($path);
                } catch (LeagueFilesystemException|UnableToRetrieveMetadata $e) {
                    return null;
                }
            },
        );
    }

    public function createResourceFromFile(PathInfoInterface $pathInfo, string $filename): ResourceInterface
    {
        return match (true) {
            (bool) filter_var($filename, FILTER_VALIDATE_URL) => $this->getResourceFromUrl(
                pathInfo: $pathInfo,
                url: $filename,
            ),
            file_exists($filename) && is_file($filename) => $this->getResourceFromLocalFile(
                pathInfo: $pathInfo,
                filename: $filename,
            ),
            default => throw new FileNotFoundException($filename),
        };
    }

    /**
     * @throws FilesystemException
     */
    private function getResourceFromUrl(PathInfoInterface $pathInfo, string $url): ResourceInterface
    {
        error_clear_last();

        $context = stream_context_create(
            options: [
                'http' => [
                    'method' => 'GET',
                    'protocol_version' => 1.1,
                    'header' => "Accept-language: en\r\n" . "User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36\r\n",
                ],
            ],
        );

        $source = @fopen(
            filename: $url,
            mode: 'rb',
            context: $context,
        );

        if (false === $source) {
            throw new FilesystemException(
                message: sprintf(
                    'Can not read stream from url "%s". %s',
                    $url,
                    error_get_last()['message'] ?? '',
                ),
            );
        }

        $headers = stream_get_meta_data($source)['wrapper_data'] ?? [];

        return new StreamResource(
            pathInfo: $pathInfo,
            source: $source,
            mimeType: function () use ($headers): ?string {
                $contentTypeHeader = $this->getHeaderValue(
                    headers: $headers,
                    name: 'Content-Type',
                );

                if (null === $contentTypeHeader) {
                    return null;
                }

                $parts = explode(
                    separator: ';',
                    string: $contentTypeHeader,
                );

                return array_shift($parts);
            },
            filesize: function () use ($headers): ?int {
                $filesize = $this->getHeaderValue(
                    headers: $headers,
                    name: 'Content-Length',
                );

                return null !== $filesize ? (int) $filesize : null;
            },
        );
    }

    /**
     * @throws FilesystemException
     */
    private function getResourceFromLocalFile(PathInfoInterface $pathInfo, string $filename): ResourceInterface
    {
        error_clear_last();

        $source = @fopen(
            filename: $filename,
            mode: 'rb',
        );

        if (false === $source) {
            throw new FilesystemException(
                message: sprintf(
                    'Can not read stream from file "%s". %s',
                    $filename,
                    error_get_last()['message'] ?? '',
                ),
            );
        }

        return new StreamResource(
            pathInfo: $pathInfo,
            source: $source,
            mimeType: function (StreamResource $resource): ?string {
                $mimeType = @mime_content_type($resource->getSource());

                return false === $mimeType ? null : $mimeType;
            },
            filesize: function () use ($filename): ?int {
                $filesize = @filesize(
                    filename: $filename,
                );

                return false === $filesize ? null : $filesize;
            },
        );
    }

    /**
     * @param array<int, string> $headers
     */
    private function getHeaderValue(array $headers, string $name): ?string
    {
        $name = strtolower($name);

        foreach ($headers as $header) {
            $header = trim(strtolower($header));

            if (!str_starts_with($header, $name . ':')) {
                continue;
            }

            $value = substr(
                string: $header,
                offset: strlen($name) + 1,
            );

            return trim($value);
        }

        return null;
    }
}
