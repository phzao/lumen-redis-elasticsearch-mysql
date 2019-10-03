<?php declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use App\Models\ModelInterface;
use App\Repositories\ElasticSearch\ElasticSearchRepositoryInterface;
use App\Repositories\Redis\RedisRepositoryInterface;
use App\Utils\Errors\ErrorMessageInterface;

/**
 * Class CategoryRepository
 * @package App\Repositories
 */
class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    /**
     * CategoryRepository constructor.
     *
     * @param ErrorMessageInterface            $errorMessage
     * @param ElasticSearchRepositoryInterface $elastic
     * @param RedisRepositoryInterface         $redis
     */
    public function __construct(ErrorMessageInterface $errorMessage,
                                ElasticSearchRepositoryInterface $elastic,
                                RedisRepositoryInterface $redis)
    {
        parent::__construct($errorMessage, $elastic, $redis);

        $this->model = new Category();
    }

    /**
     * @param array $parameters
     *
     * @return array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed
     */
    public function all(array $parameters)
    {
        $redisKey = $this->model->getRedisKeyAll($parameters);

        if ($result = $this->redis->get($redisKey)) {
            return $result;
        }

        $elasticIndex = $this->model->getElasticIndex();

        if ($this->elastic->isIndexExist($elasticIndex)) {
            $params = empty($parameters)?
                                $elasticIndex:
                                $this->model->getSearchParams($parameters);
            $result = $this->elastic->search($params);
        }

        if (empty($result)) {
            $result = parent::all($parameters);
            $result = empty($result) ? [] : $result->toArray();
        }

        if (!empty($result)) {
            $this->redis->add($redisKey, $result);
        }

        return $result;
    }

    /**
     * @param $id
     *
     * @return null|ModelInterface|mixed
     */
    public function getById($id)
    {
        $redisKey = "category-$id";

        if ($data = $this->redis->get($redisKey)) {
            return $data;
        }

        $index = "categories-table";

        if ($data = $this->elastic->get($index, $id)) {
            $this->redis->add($redisKey, $data);
            return $data;
        }

        $data = parent::getById($id);

        if (!$data) {
            return $data;
        }

        $this->redis->add($redisKey, $data->getFullDetails());
        $this->elastic->index($data->getFullDataToIndex());

        return $data;
    }

    /**
     * @param       $id
     * @param array $data
     *
     * @return mixed|void
     * @throws \Exception
     */
    public function update($id, array $data)
    {
        $category = parent::update($id, $data);

        $elasticData = $category->getFullDataToUpdateIndex();

        $this->elastic->update($elasticData);

        $redisKey = "category-$category->id";

        $this->redis->delete($redisKey);
        $this->redis->add($redisKey, $category->getFullDetails());
    }

    public function delete($id)
    {
        $redisKey = "category-$id";
        $this->redis->delete($redisKey);

        $params = [
            "index" => "categories-table",
            "id"    => (string) $id,
            "type"  => "_doc"
        ];

        $this->elastic->delete($params);

        parent::delete($id);
    }

    /**
     * @param array $content
     *
     * @return mixed
     * @throws \Exception
     */
    public function create(array $content)
    {
        $category = parent::create($content);
        $key      = $category->getRedisKey();

        $this->redis->add($key, $category->getFullDetails());
        $this->elastic->index($category->getFullDataToIndex());

        return $category;
    }
}
