<?php declare(strict_types=1);

namespace App\Models;

/**
 * Interface ModelInterface
 * @package App\Models
 */
interface ModelInterface 
{
    /**
     * @param null $id
     *
     * @return array
     */
    public function rules($id = null): array;

    /**
     * @return array
     */
    public function getFullDetails(): array;

    /**
     * @return array
     */
    public function getFullDataToIndex(): array;

    /**
     * @return array
     */
    public function getFullDataToUpdateIndex(): array;

    /**
     * @return array
     */
    public function getRulesID(): array;

    /**
     * @param null $id
     *
     * @return string
     */
    public function getRedisKey($id = null): string;

    /**
     * @param array $data
     *
     * @return string
     */
    public function getRedisKeyAll(array $data): string;

    /**
     * @param string $column
     * @param string $format
     *
     * @return string
     */
    public function getDateTimeStringFrom(string $column, $format = "Y-m-d H:i:s"): string;

    /**
     * @param array $data
     *
     * @return array
     */
    public function getSearchParams(array $data): array;

    /**
     * @return array
     */
    public function getElasticIndex(): array;
}
