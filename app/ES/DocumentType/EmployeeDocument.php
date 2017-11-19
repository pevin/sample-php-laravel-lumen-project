<?php

namespace App\ES\DocumentType;

use App\ES\ESDataType;

class EmployeeDocument extends ESDocumentType
{
    const TYPE = 'employee';

    protected $mapping = [
        'company_id' => [
            'type' => ESDataType::LONG
        ],
        'employee_number' => [
            'type' => ESDataType::KEYWORD
        ],
        'first_name' => [
            'type' => ESDataType::KEYWORD
        ],
        'middle_name' => [
            'type' => ESDataType::KEYWORD
        ],
        'last_name' => [
            'type' => ESDataType::KEYWORD
        ],
        'date_hired' => [
            'type' => ESDataType::DATE
        ],
        'date_ended' => [
            'type' => ESDataType::DATE
        ],
        'hours_per_day' => [
            'type' => ESDataType::FLOAT
        ],
        'department_name' => [
            'type' => ESDataType::KEYWORD
        ],
        'location_name' => [
            'type' => ESDataType::KEYWORD
        ],
        'active' => [
            'type' => ESDataType::BOOLEAN
        ]
    ];
}
