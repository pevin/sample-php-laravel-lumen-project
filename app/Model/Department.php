<?php

namespace App\Model;

use App\ES\Index\DepartmentIndex;
use App\ES\DocumentType\DepartmentDocument;

class Department extends Model
{
    protected $index = DepartmentIndex::NAME;

    protected $type = DepartmentDocument::TYPE;
}
