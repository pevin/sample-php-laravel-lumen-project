<?php

namespace Tests\Model;

use App\Model\Model;
use Basemkhirat\Elasticsearch\Query;

class ModelTest extends \Tests\TestCase
{
    public function testNewQuery()
    {
        $model = new Model();
        $this->assertInstanceOf(Query::class, $model->newQuery());
    }

    public function testToArray()
    {
        $model = new Model([
            'name' => 'name',
            '_type' => '_type',
            '_type' => '_type',
            '_id' => '_id',
            '_score' => '_score',
        ]);
        $this->assertInstanceOf(Query::class, $model->newQuery());

        $modelKeys = array_keys($model->toArray());
        $this->assertEquals(1, count($modelKeys));
    }
}
