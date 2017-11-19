<?php

namespace App\View;

use App\Filter\FilterFactory;
use App\Model\Employee;

class EmployeeView extends View
{
    protected $fieldMap = [
        'employee_number' => FilterFactory::TYPE_KEYWORD,
        'first_name' => FilterFactory::TYPE_KEYWORD,
        'middle_name' => FilterFactory::TYPE_KEYWORD,
        'last_name' => FilterFactory::TYPE_KEYWORD,
        'date_hired' => FilterFactory::TYPE_DATE,
        'date_ended' => FilterFactory::TYPE_DATE,
        'hours_per_day' => FilterFactory::TYPE_NUMBER,
        'department_name' => FilterFactory::TYPE_KEYWORD,
        'location_name' => FilterFactory::TYPE_KEYWORD,
        'active' => FilterFactory::TYPE_BOOLEAN
    ];

    protected $requiredColumns = [
        'first_name' => "First name",
        'last_name' => "Last name",
        'employee_number' => "Employee number",
    ];

    protected $optionalColumns = [
        'middle_name' => "Middle name",
        'date_hired' => "Date hired",
        'date_ended' => "Date ended",
        'hours_per_day' => "Hours per day",
        'department_name' => "Department Name",
        'location_name' => "Location Name",
        'active' => "Active"
    ];

    protected $defaultSort = [
        [
            'field' => 'employee_number',
            'order' => 'asc'
        ]
    ];

    protected function getModel()
    {
        return new Employee();
    }
}
