<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Tests\Config;

use SixtyEightPublishers\FileStorage\Config\Config;
use SixtyEightPublishers\FileStorage\Config\ConfigInterface;
use SixtyEightPublishers\FileStorage\Exception\IllegalMethodCallException;
use SixtyEightPublishers\FileStorage\Exception\InvalidArgumentException;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

final class ConfigTest extends TestCase
{
    public function testOffsetGet(): void
    {
        $config = new Config([]);

        Assert::same('', $config[ConfigInterface::BASE_PATH]);
        Assert::null($config[ConfigInterface::HOST]);
        Assert::same('_v', $config[ConfigInterface::VERSION_PARAMETER_NAME]);
    }

    public function testExceptionShouldBeThrownOnInvalidOffset(): void
    {
        $config = new Config([]);

        Assert::exception(
            static fn () => $config['missing'],
            InvalidArgumentException::class,
            'Missing a configuration option "missing".',
        );
    }

    public function testOffsetExists(): void
    {
        $config = new Config([]);

        Assert::true(isset($config[ConfigInterface::BASE_PATH]));
        Assert::true(isset($config[ConfigInterface::HOST]));
        Assert::true(isset($config[ConfigInterface::VERSION_PARAMETER_NAME]));
    }

    public function testExceptionShouldBeThrownOnOffsetSet(): void
    {
        Assert::exception(
            static function () {
                $config = new Config([]);
                $config[ConfigInterface::BASE_PATH] = '/';
            },
            IllegalMethodCallException::class,
            'Calling the method SixtyEightPublishers\FileStorage\Config\Config::offsetSet() is not allowed.',
        );
    }

    public function testExceptionShouldBeThrownOnOffsetUnset(): void
    {
        Assert::exception(
            static function () {
                $config = new Config([]);
                unset($config[ConfigInterface::BASE_PATH]);
            },
            IllegalMethodCallException::class,
            'Calling the method SixtyEightPublishers\FileStorage\Config\Config::offsetUnset() is not allowed.',
        );
    }

    public function testSlashesShouldBeRemovedFromBasePathAndHostOptions(): void
    {
        $config = new Config([
            ConfigInterface::BASE_PATH => '/files/',
            ConfigInterface::HOST => 'https://www.example.com/',
        ]);

        Assert::same('files', $config[ConfigInterface::BASE_PATH]);
        Assert::same('https://www.example.com', $config[ConfigInterface::HOST]);
    }

    public function testConfigShouldBeExtendedWithCustomOptions(): void
    {
        $config = new Config([
            'my_option' => 123,
        ]);

        Assert::same(123, $config['my_option']);
    }

    public function testConfigShouldBeSerializedToJson(): void
    {
        $config = new Config([
            'my_option' => 123,
        ]);

        Assert::same(
            '{"base_path":"","host":null,"version_parameter_name":"_v","my_option":123}',
            json_encode($config, JSON_THROW_ON_ERROR),
        );
    }
}

(new ConfigTest())->run();
