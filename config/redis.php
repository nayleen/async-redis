<?php

declare(strict_types = 1);

use Amp\Redis\RedisCache;
use Amp\Redis\RedisClient;
use Amp\Redis\Sync\RedisMutex;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use function Amp\Redis\createRedisClient;

return [
    // parameters
    'redis.dsn' => DI\env('REDIS_DSN', 'redis://localhost:6379'),

    'redis.enabled' => DI\bool('REDIS_ENABLED', false),

    // services
    RedisCache::class => DI\factory(static function (DI\Container $c) {
        $client = $c->make(RedisClient::class);
        assert($client instanceof RedisClient);

        return new RedisCache($client);
    }),

    RedisClient::class => DI\factory(static function (ContainerInterface $c, bool $enabled) {
        if (!$enabled) {
            throw new RuntimeException('Set REDIS_ENABLED=1 to use ' . RedisClient::class);
        }

        $dsn = $c->get('redis.dsn');
        assert(is_string($dsn));

        return createRedisClient($dsn);
    })
        ->parameter('enabled', DI\get('redis.enabled')),

    RedisMutex::class => DI\factory(static function (DI\Container $c) {
        $client = $c->make(RedisClient::class);
        assert($client instanceof RedisClient);

        return new RedisMutex(
            client: $client,
            logger: $c->get(LoggerInterface::class),
        );
    }),
];
