<?php

namespace App\Filter;

abstract class Filter
{
    const EQUALS = '=';
    const EMPTY = 'empty';
    const MATCH = 'match';
    const PREFIX = 'prefix';
    const GT = '>';
    const GTE = '>=';
    const LT = '<';
    const LTE = '<=';
    const TRUE = 'true';
    const FALSE = 'false';

    const SEARCH_GT = 'gt';
    const SEARCH_GTE = 'gte';
    const SEARCH_LT = 'lt';
    const SEARCH_LTE = 'lte';

    protected $field;

    protected $condition;

    protected $value;

    /**
     *
     * Initialize filter
     *
     * @param string $field
     * @param string $condition
     * @param string $value
     *
     */
    public function __construct(
        string $field,
        string $condition,
        string $value = null
    ) {
        $this->setField($field);
        $this->setCondition($condition);
        $this->setValue($value);
    }

    public function getField()
    {
        return $this->field;
    }

    public function setField(string $field)
    {
        $this->field = $field;
    }

    public function getCondition()
    {
        return $this->condition;
    }

    public function setCondition(string $condition)
    {
        $this->condition = $condition;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue(string $value = null)
    {
        $this->value = $value;
    }

    public function searchCondition()
    {
        switch ($this->condition) {
            case self::GT:
                return self::SEARCH_GT;
                break;
            case self::GTE:
                return self::SEARCH_GTE;
                break;
            case self::LT:
                return self::SEARCH_LT;
                break;
            case self::LTE:
                return self::SEARCH_LTE;
                break;
            case self::TRUE:
                return true;
                break;
            case self::FALSE:
                return false;
                break;
            default:
                throw new \Exception('Invalid search condition');
        }
    }

    /**
     * Get filters in form options format
     * @return array
     */
    public static function getOptions()
    {
        $conditionCollection = collect(static::ALLOWED_CONDITIONS);
        $conditionCollection = $conditionCollection->map(function ($item, $key) {
            return [
                'label' => $key,
                'value' => $item
            ];
        });

        return $conditionCollection->values()->all();
    }

    abstract protected function buildSearchParameters();
}
