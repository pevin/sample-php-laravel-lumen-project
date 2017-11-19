<?php

namespace App\ES\Index;

use App\ES\DocumentType\DepartmentDocument;

class DepartmentIndex extends ESIndex
{
    const NAME = 'department_index';

    const DEFAULT_NUMBER_SHARDS = 1;
    const DEFAULT_NUMBER_REPLICAS = 0;

    protected $aliases = [
        "department"
    ];

    public function getSettings()
    {
        return [
            "number_of_shards" => env('DEPARTMENT_INDEX_SHARDS', self::DEFAULT_NUMBER_SHARDS),
            "number_of_replicas" => env('DEPARTMENT_INDEX_REPLICAS', self::DEFAULT_NUMBER_REPLICAS),
        ];
    }

    public static function getDocumentType()
    {
        return DepartmentDocument::TYPE;
    }

    public static function getDocumentMapping()
    {
        return DepartmentDocument::getMapping();
    }
}
