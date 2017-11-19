<?php

namespace App\ES\Index;

abstract class ESIndex
{
    public static function getIndexConfig()
    {
        $instance = new static;
        return [
            'aliases' => $instance->aliases,
            'settings' => $instance->getSettings(),
            'mappings' => $instance->getMappings()
        ];
    }

    public static function getMappings()
    {
        return [
            static::getDocumentType() => [
                'properties' => static::getDocumentMapping()
            ],
        ];
    }

    abstract public function getSettings();

    abstract public static function getDocumentType();

    abstract public static function getDocumentMapping();
}
