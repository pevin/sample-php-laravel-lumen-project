<?php

namespace App\Model;

use Basemkhirat\Elasticsearch\Model as BaseModel;

class Model extends BaseModel
{
    const ELASTICSEARCH_KEYS = [
        '_index',
        '_type',
        '_id',
        '_score',
    ];

    public function newQuery()
    {
        return parent::newQuery();
    }

    /**
     * Get model as array, but removes elastic search keys
     *
     * @return array
     */
    public function toArray()
    {

        $attributes = parent::toArray();

        return array_except($attributes, self::ELASTICSEARCH_KEYS);
    }
}
