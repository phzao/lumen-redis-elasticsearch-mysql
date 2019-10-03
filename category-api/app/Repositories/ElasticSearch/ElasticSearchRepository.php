<?php declare(strict_types=1);

namespace App\Repositories\ElasticSearch;

use App\Utils\Errors\ErrorMessageInterface;
use Elasticsearch\ClientBuilder;

/**
 * Class ElasticSearchRepository
 * @package App\Repositories\ElasticSearch
 */
class ElasticSearchRepository implements ElasticSearchRepositoryInterface
{
    /**
     * @var \Elasticsearch\Client
     */
    private $clientBuilder;

    /**
     * @var mixed
     */
    private $url;

    private $errorMsg;

    /**
     * @var ClientBuilder
     */
    private $builder;

    /**
     * ElasticSearchRepository constructor.
     *
     * @param ClientBuilder         $clientBuilder
     * @param ErrorMessageInterface $errorMessage
     */
    public function __construct(ClientBuilder $clientBuilder,
                                ErrorMessageInterface $errorMessage)
    {
        $this->url      = env('APP_ENV')==='testing'?
                                    config('services.elasticSearchTest.base_uri'):
                                    config('services.elasticSearch.base_uri');

        $this->builder  = $clientBuilder;
        $this->errorMsg = $errorMessage;
    }

    /**
     * @return null|bool
     */
    private function isOnlineElasticSearch()
    {
        try {
            $this->clientBuilder = $this->builder::create()
                                        ->setHosts([$this->url])
                                        ->build();

            $this->clientBuilder->info();

            return true;
        } catch (\Exception $exception) {
//            $errormsg = $this->errorMsg
//                             ->getErrorMessage('error', "ElasticSearch server is offline!");

            return null;
//            throw new \Exception($errormsg);
        }
    }

    /**
     * @param array $data
     *
     * @return bool|mixed
     */
    public function index(array $data)
    {
        if(!$this->isOnlineElasticSearch()) {
            return false;
        }

        try {

            $this->clientBuilder->index($data);
            return true;
        } catch (\Exception $exception) {

            return false;
        }
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function delete(array $data)
    {
        if(!$this->isOnlineElasticSearch()) {
            return false;
        }

        try {

            $this->clientBuilder->delete($data);
            return true;
        } catch (\Exception $exception) {

            return false;
        }
    }

    /**
     * @param array $data
     *
     * @return bool|mixed
     */
    public function update(array $data)
    {
        if(!$this->isOnlineElasticSearch()) {
            return false;
        }

        try {

            $this->clientBuilder->update($data);
            return true;
        } catch (\Exception $exception) {

            return false;
        }
    }

    /**
     * @param string $index
     * @param        $id
     *
     * @return array|callable|mixed
     * @throws \Exception
     */
    public function get(string $index, $id)
    {

        if(!$this->isOnlineElasticSearch()) {
            return false;
        }

        try {
            $params = [
                'index' => $index,
                'id'    => $id
            ];

            $data = $this->clientBuilder->get($params);

            return $data['_source'];

        } catch (\Exception $exception) {

            return [];
        }
    }

    /**
     * @param $array
     *
     * @return array|bool
     */
    public function search($array): array
    {
        if(!$this->isOnlineElasticSearch()) {
            return false;
        }

        $result = $this->clientBuilder->search($array);
        $hits   = $result['hits'];
        $list   = $hits['hits'];
        $data   = [];

        foreach ($list as $item)
        {
            $data[] = $item['_source'];
        }

        return [
            "took"    => $result["took"],
            "total"   => $hits["total"],
            "results" => $data
        ];
    }

    /**
     * @param array $index
     */
    public function clearIndex(array $index)
    {
        $this->clientBuilder->indices()->delete($index);
    }

    /**
     * @param array $index
     *
     * @return bool|mixed
     */
    public function isIndexExist(array $index)
    {
        if(!$this->isOnlineElasticSearch()) {
            return false;
        }

        return $this->clientBuilder->indices()->exists($index);
    }

    /**
     * @param string $index
     * @param        $id
     *
     * @return array|callable|mixed
     */
    public function getSource(string $index, $id)
    {
        try {
            $params = [
                'index' => $index,
                'id'    => $id
            ];

            $data = $this->clientBuilder->get($params);

            return $data["_source"];

        } catch (\Exception $exception) {

            return [];
        }
    }
}
