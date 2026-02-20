<?php

declare(strict_types=1);

namespace SixtyEightPublishers\FileStorage\LinkGenerator;

use SixtyEightPublishers\FileStorage\PathInfoInterface;

interface LinkGeneratorInterface
{
    public function link(PathInfoInterface $pathInfo, bool $absolute = true): string;
}
