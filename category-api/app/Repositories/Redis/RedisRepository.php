<?php declare(strict_types=1);

namespace App\Repositories\Redis;

use App\Utils\Errors\ErrorMessageInterface;
use App\Utils\Hash\HashMD5;
use Predis\Client;

/**
 * Class RedisRepository
 */
class RedisRepository implements RedisRepositoryInterface
{
    /**
     * @var Client
     */
    private $redis;

    /**
     * @var ErrorMessageInterface
     */
    private $errorMsg;

    /**
     * @var HashMD5
     */
    private $crypto;


    CONST TTL = 3600;

    CONST EXPIRERESOLUTION = 'EX';

    /**
     * RedisRepository constructor.
     *
     * @param ErrorMessageInterface $errorMessage
     */
    public function __construct(ErrorMessageInterface $errorMessage)
    {
        $env_redis = env('APP_ENV')==='testing'?
                                env('REDIS_HOST_TEST'):
                                env('REDIS_HOST');

        $this->redis = new Client(
            [
                'scheme' => 'tcp',
                'host'   => $env_redis,
                'port'   => 6379
            ]
        );

        $this->errorMsg = $errorMessage;
        $this->crypto   = new HashMD5();
    }

    /**
     * @return bool|\Exception
     */
    public function isOnlineRedis()
    {
        try {
            $this->redis->connect();
            return true;
        } catch (\Exception $exception) {

//            $errormsg = $this->errorMsg
//                             ->getErrorMessage("error", "Redis server is probably offline!");

            return null;
//            throw new \Exception(json_encode($errormsg));
        }
    }

    /**
     * @param string $key
     * @param array  $data
     *
     * @return bool|mixed
     */
    public function add(string $key, array $data)
    {
        if (!$this->isOnlineRedis()) {
            return false;
        }

        try {
            $redis_key = $this->crypto->getCrypto($key);
            $data      = json_encode($data);

            $this->redis->setex($redis_key, self::TTL, $data);

            return true;
        } catch (\Exception $exception) {
//            $msg      = $exception->getMessage();
//            $errormsg = $this->errorMsg
//                             ->getErrorMessage("error", "Error trying to record to Redis! - $msg");
//
//            throw new \Exception(json_encode($errormsg));
            return false;
        }
    }

    /**
     * @param string $key
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public function delete(string $key)
    {
        if (!$this->isOnlineRedis()) {
            return false;
        }

        try {
            $redis_key = $this->crypto->getCrypto($key);

            $this->redis->del([$redis_key]);

            return true;
        } catch (\Exception $exception) {
            $msg      = $exception->getMessage();
            $errormsg = $this->errorMsg
                             ->getErrorMessage("error", "Error trying to delete to Redis! - $msg");

            throw new \Exception(json_encode($errormsg));
        }
    }

    /**
     * @param string $key
     *
     * @return bool|mixed|string
     * @throws \Exception
     */
    public function get(string $key)
    {
        if (!$this->isOnlineRedis()) {
            return false;
        }

        try {
            $redis_key = $this->crypto->getCrypto($key);

            $record = $this->redis->get($redis_key);

            if ( empty($record) ){
                return $record;
            }

            return json_decode($record, true);

        } catch (\Exception $exception) {
            $msg      = $exception->getMessage();
            $errormsg = $this->errorMsg
                             ->getErrorMessage("error", "Error trying to record to Redis! - $msg");

            throw new \Exception(json_encode($errormsg));
        }
    }

    public function addList()
    {
        
    }
}
