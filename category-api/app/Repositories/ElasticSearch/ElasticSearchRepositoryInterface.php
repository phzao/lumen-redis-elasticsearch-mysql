<?php declare(strict_types=1);

namespace App\Repositories\ElasticSearch;

/**
 * Interface ElasticSearchRepositoryInterface
 * @package App\Repositories\ElasticSearch
 */
interface ElasticSearchRepositoryInterface
{
    /**
     * @param array $data
     *
     * @return mixed
     */
    public function index(array $data);

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function update(array $data);

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function delete(array $data);

    /**
     * @param array $index
     *
     * @return mixed
     */
    public function isIndexExist(array $index);

    /**
     * @param string $index
     * @param        $id
     *
     * @return mixed
     */
    public function get(string $index, $id);

    /**
     * @param string $index
     * @param        $id
     *
     * @return mixed
     */
    public function getSource(string $index, $id);

    /**
     * @param array $array
     *
     * @return array
     */
    public function search(array $array): array;
}
