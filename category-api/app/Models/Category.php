<?php declare(strict_types=1);

namespace App\Models;

/**
 * Class User
 * @package App\Models
 */
class Category extends ModelBase implements CategoryInterface
{
    const INDEX_ELASTIC_SEARCH = 'categories-table';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * @return array
     */
    public function getFullDataToIndex(): array
    {
        return [
            'index' => self::INDEX_ELASTIC_SEARCH,
            'id'    => $this->getAttribute('id'),
            'body'  => $this->getFullDetails()
        ];
    }

    /**
     * @return array
     */
    public function getFullDataToUpdateIndex(): array
    {
        return [
            'index' => self::INDEX_ELASTIC_SEARCH,
            'id'    => (string) $this->getAttribute('id'),
            'type'  => '_doc',
            'body'  => [
                "doc" => $this->getFullDetails()
            ]
        ];
    }

    /**
     * @param array  $data
     * @param string $field
     *
     * @return array
     */
    public function getStringField(array $data, string $field): array
    {
        if (!empty($data[$field])) {
            return [
                "query_string" => $data[$field]."*",
                "field"        => "$field"
            ];
        }

        return [];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function getSearchParams(array $data): array
    {
        $strings = $this->getStringField($data, "name");

        if (empty($strings)) {
            $strings = $this->getStringField($data, "description");
        }

        if (empty($strings)) {
            return [];
        }

        $query = empty($strings['query_string']) ? "" : $strings['query_string'];
        $field = empty($strings['field']) ? "" : $strings['field'];

        return [
            "index" => self::INDEX_ELASTIC_SEARCH,
            "body"  => [
                "query" => [
                    "simple_query_string" => [
                        "query"  => $query,
                        "fields" => $field
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getElasticIndex():array
    {
        return [
            "index" => self::INDEX_ELASTIC_SEARCH
        ];
    }

    /**
     * @param string $column
     * @param string $format
     *
     * @return string
     */
    public function getDateTimeStringFrom(string $column, $format = "Y-m-d H:i:s"): string
    {
        return parent::getDateTimeStringFrom($column, $format);
    }

    /**
     * @param null $id
     *
     * @return array
     */
    public function rules($id = null): array
    {
        $id        = empty($id) ? "" : ",".$id;
        $sometimes = empty($id) ? "" : "sometimes|";

        $attributes = [
            'name'        => $sometimes."required|string|max:50|min:2",
            'description' => $sometimes."nullable|string|min:10|max:250"
        ];

        return $attributes;
    }

    /**
     * @param null $id
     *
     * @return string
     */
    public function getRedisKey($id = null): string
    {
        $idCategory = empty($id) ? $this->getAttribute('id') : $id;

        return "category-$idCategory";
    }

    /**
     * @param array $data
     *
     * @return string
     */
    public function getRedisKeyAll(array $data): string
    {
        $parameters = "" ;

        if (!empty($data["name"])) {
            $parameters .= "-name-".$data["name"];
        }

        if (!empty($data["description"])) {
            $parameters .= "-description-".$data["description"];
        }
        return "category-all".$parameters;
    }

    /**
     * @return array
     */
    public function getFullDetails(): array
    {
        return [
            'id'          => (string) $this->getAttribute('id'),
            'name'        => $this->getAttribute('name'),
            'description' => $this->getAttribute('description'),
            'updated_at'  => $this->getDateTimeStringFrom('updated_at'),
            'created_at'  => $this->getDateTimeStringFrom('created_at'),
        ];
    }
}
