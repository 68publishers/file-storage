<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Config;

use SixtyEightPublishers\FileStorage\Exception\InvalidArgumentException;
use SixtyEightPublishers\FileStorage\Exception\IllegalMethodCallException;

class Config implements ConfigInterface
{
	/** @var array  */
	protected $config = [
		self::BASE_PATH => '',
		self::HOST => NULL,
		self::VERSION_PARAMETER_NAME => '_v',
	];

	/**
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$this->config = array_merge($this->config, $config);

		// trim base path
		$this->config[self::BASE_PATH] = trim((string) $this->config[self::BASE_PATH], '/');

		if (!empty($this->config[self::HOST])) {
			$this->config[self::HOST] = rtrim((string) $this->config[self::HOST], '/');
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetExists($offset): bool
	{
		return array_key_exists($offset, $this->config);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetGet($offset)
	{
		if (!$this->offsetExists($offset)) {
			throw new InvalidArgumentException(sprintf(
				'Missing a configuration option %s',
				(string) $offset
			));
		}

		return $this->config[$offset];
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetSet($offset, $value): void
	{
		throw IllegalMethodCallException::notAllowed(__METHOD__);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetUnset($offset): void
	{
		throw IllegalMethodCallException::notAllowed(__METHOD__);
	}

	/**
	 * {@inheritdoc}
	 */
	public function jsonSerialize(): array
	{
		return $this->config;
	}
}
