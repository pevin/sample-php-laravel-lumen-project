<?php

namespace App\Filter\Query;

trait RangeQueryTrait
{
    /**
     * Construct search parameters for range query
     * @return array
     */
    protected function buildRangeSearchParameters()
    {
        return [
            "bool" => [
                "must" => [
                    "range" => [
                        $this->field => [
                            $this->searchCondition() => $this->getValue()
                        ]
                    ],
                ]
            ]
        ];
    }
}
