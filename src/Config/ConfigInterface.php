<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\Config;

use ArrayAccess;
use JsonSerializable;

/**
 * @extends ArrayAccess<string, mixed>
 */
interface ConfigInterface extends ArrayAccess, JsonSerializable
{
    public const BASE_PATH = 'base_path';
    public const HOST = 'host';
    public const VERSION_PARAMETER_NAME = 'version_parameter_name';
}
