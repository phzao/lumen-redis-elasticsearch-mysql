<?php declare(strict_types=1);

namespace App\Repositories;

use App\Models\ModelInterface;
use App\Repositories\ElasticSearch\ElasticSearchRepositoryInterface;
use App\Repositories\Redis\RedisRepositoryInterface;
use App\Utils\Errors\ErrorMessageInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\Exception\InvalidResourceException;

/**
 * Class BaseRepository
 * @package App\Repository
 */
class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var ErrorMessageInterface
     */
    protected $errorMsg;

    /**
     * @var ElasticSearchRepositoryInterface
     */
    protected $elastic;

    /**
     * @var RedisRepositoryInterface
     */
    protected $redis;

    /**
     * BaseRepository constructor.
     *
     * @param ErrorMessageInterface            $errorMessage
     * @param ElasticSearchRepositoryInterface $elastic
     * @param RedisRepositoryInterface         $redis
     */
    public function __construct(ErrorMessageInterface $errorMessage,
                                ElasticSearchRepositoryInterface $elastic,
                                RedisRepositoryInterface $redis)
    {
        $this->errorMsg = $errorMessage;
        $this->elastic  = $elastic;
        $this->redis    = $redis;
    }

    public function isOnlineMysql()
    {
        try {

            if(env('APP_ENV')!=='testing') {
//                $this->elastic->index($data->getFullDataToIndex());
            }

            DB::connection()->getPdo();

        } catch (\PDOException $exception) {
            $msg = $this->errorMsg->getErrorMessage("error",
                                          "MySQL Database is unavailable!");
            throw new \PDOException($msg);
        }
    }

    /**
     * @param array $content
     *
     * @return mixed
     * @throws \Exception
     */
    public function create(array $content)
    {
        $this->isOnlineMysql();
        try {
             return $this->model::create($content);
        } catch (QueryException $e) {
            $msg = $this->errorMsg->getErrorMessage("error", "Error trying to record data on MySQL!");
            throw new \Exception($msg);
        }
    }

    /**
     * @param array $parameters
     *
     * @return \Illuminate\Database\Eloquent\Collection|Model[]|mixed
     */
    public function all(array $parameters)
    {
        return $this->model::all();
    }

    /**
     * @param array $content
     *
     * @return \Illuminate\Database\Eloquent\Collection|Model[]|mixed
     */
    public function allBy(array $content)
    {
        if (empty($content)) {
            return $this->model::all();
        }
    }


    /**
     * @param $id
     *
     * @return int|mixed
     * @throws \Exception
     */
    public function delete($id)
    {
        $status = $this->model::destroy($id);

        if (!$status) {
            throw new \Exception("Record does not exist!");
        }

        return $status;
    }

    /**
     * @param $id
     *
     * @return null|ModelInterface
     */
    public function getById($id)
    {
        $this->isOnlineMysql();

        $data = $this->model::find($id);

        if (!$data) {
            $msg = $this->errorMsg->getErrorMessage("fail", "No records with this ID!");
            throw new InvalidResourceException($msg);
        }

        return $data;
    }

    /**
     * @param       $id
     * @param array $content
     *
     * @return mixed
     * @throws \Exception
     */
    public function update($id, array $content)
    {
        $this->isOnlineMysql();

        $register = $this->model::find($id);

        if (empty($register)) {
            $msg = $this->errorMsg
                        ->getErrorMessage("error", "There is not register to this ID!");

            throw new NotFoundHttpException($msg);
        }

        try {
            $register->update($content);

            return $register;
        } catch (QueryException $e) {
            $msg = $this->errorMsg
                        ->getErrorMessage("error", "Error trying to record data on MySQL!");

            throw new \Exception($msg);
        }
    }
}
