<?php declare(strict_types=1);

namespace App\Repositories\Redis;

/**
 * Class RedisRepositoryInterface\
 */
interface RedisRepositoryInterface
{
    /**
     * @param string $key
     * @param array  $data
     *
     * @return mixed
     */
    public function add(string $key, array $data);

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key);

    /**
     * @return \Exception
     */
    public function isOnlineRedis();

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function delete(string $key);

    /**
     * @return mixed
     */
    public function addList();
}
