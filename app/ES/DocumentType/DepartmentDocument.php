<?php

namespace App\ES\DocumentType;

use App\ES\ESDataType;

class DepartmentDocument extends ESDocumentType
{
    const TYPE = 'department';

    protected $mapping = [
        'company_id' => [
            'type' => ESDataType::LONG
        ],
        'name' => [
            'type' => ESDataType::KEYWORD
        ],
        'parent_name' => [
            'type' => ESDataType::KEYWORD
        ],
    ];
}
