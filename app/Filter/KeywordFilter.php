<?php

namespace App\Filter;

use App\Filter\Query\EqualsQueryTrait;

class KeywordFilter extends Filter
{
    use EqualsQueryTrait;

    const ALLOWED_CONDITIONS = [
        '=' => self::EQUALS,
        'Starts with' => self::PREFIX,
    ];

    public function buildSearchParameters()
    {
        switch ($this->condition) {
            case self::EQUALS:
                return $this->buildEqualsSearchParameters();
                break;
            case self::PREFIX:
                return $this->buildPrefixSearchParameters();
                break;
            default:
                throw new \InvalidArgumentException('Invalid condition');
        }
    }

    /**
     *
     *  Construct search parameters for contains match
     *
     */
    protected function buildPrefixSearchParameters()
    {
        return [

        "prefix" => [
            $this->field => [
                    "value" => $this->value
            ]
        ]

        ];
    }
}
