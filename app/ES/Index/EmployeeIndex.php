<?php

namespace App\ES\Index;

use App\ES\DocumentType\EmployeeDocument;

class EmployeeIndex extends ESIndex
{
    const NAME = 'employee_index';

    const DEFAULT_NUMBER_SHARDS = 1;
    const DEFAULT_NUMBER_REPLICAS = 0;

    protected $aliases = [
        "employee"
    ];

    public function getSettings()
    {
        return [
            "number_of_shards" => env('EMPLOYEE_INDEX_SHARDS', self::DEFAULT_NUMBER_SHARDS),
            "number_of_replicas" => env('EMPLOYEE_INDEX_REPLICAS', self::DEFAULT_NUMBER_REPLICAS),
        ];
    }

    public static function getDocumentType()
    {
        return EmployeeDocument::TYPE;
    }

    public static function getDocumentMapping()
    {
        return EmployeeDocument::getMapping();
    }
}
