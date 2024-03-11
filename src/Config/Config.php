<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Config;

use SixtyEightPublishers\FileStorage\Exception\IllegalMethodCallException;
use SixtyEightPublishers\FileStorage\Exception\InvalidArgumentException;
use function array_key_exists;
use function array_merge;
use function is_string;
use function rtrim;
use function sprintf;
use function trim;

class Config implements ConfigInterface
{
    /** @var array<string, mixed> */
    protected array $config = [
        self::BASE_PATH => '',
        self::HOST => null,
        self::VERSION_PARAMETER_NAME => '_v',
    ];

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config)
    {
        $this->config = array_merge($this->config, $config);

        // trim base path
        $this->config[self::BASE_PATH] = is_string($this->config[self::BASE_PATH]) ? trim($this->config[self::BASE_PATH], '/') : '';

        if (!empty($this->config[self::HOST]) && is_string($this->config[self::HOST])) {
            $this->config[self::HOST] = rtrim($this->config[self::HOST], '/');
        }
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->config);
    }

    public function offsetGet($offset): mixed
    {
        if (!$this->offsetExists($offset)) {
            throw new InvalidArgumentException(sprintf(
                'Missing a configuration option "%s".',
                $offset,
            ));
        }

        return $this->config[$offset];
    }

    public function offsetSet($offset, $value): never
    {
        throw IllegalMethodCallException::notAllowed(__METHOD__);
    }

    public function offsetUnset($offset): never
    {
        throw IllegalMethodCallException::notAllowed(__METHOD__);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->config;
    }
}
