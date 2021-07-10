<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 2016/9/1
 * Time: 19:40
 */

namespace Polymer\Providers;

use DI\Container;
use Exception;
use Redis;

class RedisProvider
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $diContainer A container instance
     */
    public function register(Container $diContainer): void
    {
        $diContainer->set(__CLASS__, static function () use ($diContainer) {
            try {
                $serverName = $diContainer->has('redis_server') ? $diContainer->get('redis_server') : 'server1';
                $serversConfig = $diContainer->get('application')->config('cache.redis.' . $serverName);
                $redis = new Redis();
                $redis->connect($serversConfig['server']['host'], $serversConfig['server']['port'], $serversConfig['server']['timeout']);
                (isset($serversConfig['server']['password']) && $serversConfig['server']['password']) && $redis->auth($serversConfig['server']['password']);
                return $redis;
            } catch (Exception $e) {
                return null;
            }
        });
    }
}
