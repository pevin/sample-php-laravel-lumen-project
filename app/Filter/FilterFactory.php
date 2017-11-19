<?php

namespace App\Filter;

class FilterFactory
{
    const TYPE_KEYWORD = 'keyword';
    const TYPE_DATE = 'date';
    const TYPE_NUMBER = 'number';
    const TYPE_BOOLEAN = 'boolean';

    const CLASS_MAP = [
        self::TYPE_KEYWORD => KeywordFilter::class,
        self::TYPE_DATE => DateFilter::class,
        self::TYPE_NUMBER => NumberFilter::class,
        self::TYPE_BOOLEAN => BooleanFilter::class,
    ];

    /**
     * Creates a Filter type object based on given type
     *
     * @param string $type
     * @return Filter
     * @throws \InvalidArgumentException
     *
     */
    public static function getFilterClass(string $type)
    {
        if (empty(self::CLASS_MAP[$type])) {
            throw new \InvalidArgumentException('Unknown Filter type.');
        }

        return self::CLASS_MAP[$type];
    }
}
