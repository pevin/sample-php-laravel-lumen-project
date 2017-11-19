<?php

namespace App\Filter;

use App\Filter\Query\EqualsQueryTrait;
use App\Filter\Query\RangeQueryTrait;

class NumberFilter extends Filter
{
    use EqualsQueryTrait,
        RangeQueryTrait;

    const ALLOWED_CONDITIONS = [
        '=' => self::EQUALS,
        '>' => self::GT,
        '>=' => self::GTE,
        '<' => self::LT,
        '<=' => self::LTE,
    ];

    public function buildSearchParameters()
    {
        switch ($this->condition) {
            case self::EQUALS:
                return $this->buildEqualsSearchParameters();
                break;
            case self::GT:
            case self::GTE:
            case self::LT:
            case self::LTE:
                return $this->buildRangeSearchParameters();
                break;
            default:
                throw new \InvalidArgumentException('Invalid condition');
        }
    }
}
