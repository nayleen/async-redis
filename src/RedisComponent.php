<?php

declare(strict_types = 1);

namespace Nayleen\Async\Redis;

use Nayleen\Async\Component;
use Nayleen\Async\DI\Loader;

final readonly class RedisComponent extends Component
{
    public function register(Loader $loader): void
    {
        $loader->loadDir(dirname(__DIR__) . '/config');
    }
}
