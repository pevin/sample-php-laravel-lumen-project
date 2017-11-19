<?php

namespace App\ES\DocumentType;

abstract class ESDocumentType
{
    public static function getMapping()
    {
        $instance = new static;
        return $instance->mapping;
    }
}
