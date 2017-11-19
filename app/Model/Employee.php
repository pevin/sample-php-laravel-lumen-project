<?php

namespace App\Model;

use App\ES\Index\EmployeeIndex;
use App\ES\DocumentType\EmployeeDocument;

class Employee extends Model
{
    protected $index = EmployeeIndex::NAME;

    protected $type = EmployeeDocument::TYPE;
}
