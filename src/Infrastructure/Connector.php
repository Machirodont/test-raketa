<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure;

use Redis;
use RedisException;

readonly class Connector
{

    private Redis $redis;

    public function __construct(
        private string $host,
        private int $port = 6379,
        private ?string $password = null,
        private ?int $dbindex = null,
    ) {
        $this->build();
    }

    /**
     * @throws ConnectorException
     */
    public function get(string $key)
    {
        try {
            return unserialize($this->redis->get($key));
        } catch (RedisException $e) {
            throw new ConnectorException('Connector error', $e->getCode(), $e);
        }
    }

    /**
     * @throws ConnectorException
     */
    public function set(string $key, $value): void
    {
        try {
            $this->redis->setex($key, 24 * 60 * 60, serialize($value));
        } catch (RedisException $e) {
            throw new ConnectorException('Connector error', $e->getCode(), $e);
        }
    }

    public function has($key): bool
    {
        return $this->redis->exists($key);
    }

    protected function build(): void
    {
        $this->redis = new Redis();

        try {
            $isConnected = $this->redis->isConnected();
            if (! $isConnected && $this->redis->ping('Pong')) {
                $isConnected = $this->redis->connect(
                    $this->host,
                    $this->port,
                );
            }
        } catch (RedisException) {
        }

        if ($isConnected) {
            $redis->auth($this->password);
            $redis->select($this->dbindex);
        }
    }

}
