<?php

namespace App\ES;

class ESDataType
{
    // https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-types.html
    const KEYWORD = 'keyword';
    const TEXT = 'text';
    const LONG = 'long';
    const FLOAT = 'float';
    const DATE = 'date';
    const BOOLEAN = 'boolean';
}
