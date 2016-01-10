<?php

namespace Volo\FrontendBundle\Http\Session\Handler;

/**
 * RedisSessionHandler.
 *
 * Redis based session storage handler based on the PhpRedis.
 *
 * @see https://github.com/phpredis/phpredis
 */
class RedisSessionHandler implements \SessionHandlerInterface
{
    /**
     * @var \Redis PhpRedis driver.
     */
    private $redis;

    /**
     * @param \Redis $redis A \Redis instance
     */
    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * {@inheritDoc}
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function read($sessionId)
    {
        if ('' === (string) $sessionId) {
            if (!session_regenerate_id()) {
                throw new \InvalidArgumentException('Unable to regenerate sessionID.');
            }
            $sessionId = session_id();

            if (!$sessionId) {
                throw new \RuntimeException('SessionID empty on Session::read');
            }
        }

        return $this->redis->get($sessionId) ?: '';
    }

    /**
     * {@inheritDoc}
     */
    public function write($sessionId, $data)
    {
        if ('' === (string) $sessionId) {
            throw new \InvalidArgumentException('SessionID empty on Session::write');
        }

        $maxLifeTime = (int) ini_get('session.gc_maxlifetime');

        return $this->redis->setex($sessionId, $maxLifeTime, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function destroy($sessionId)
    {
        return ($this->redis->delete($sessionId) === 1);
    }

    /**
     * {@inheritDoc}
     */
    public function gc($lifetime)
    {
        // not required here because redis will auto expire the records anyhow.
        return true;
    }
}
