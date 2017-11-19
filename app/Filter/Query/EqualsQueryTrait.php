<?php

namespace App\Filter\Query;

trait EqualsQueryTrait
{
    /**
     * Construct search query for exact value
     * @return array
     */
    protected function buildEqualsSearchParameters()
    {
        return [
            "bool" => [
                "must" => [
                    [
                        "term" => [
                            $this->field => $this->getValue()
                        ],
                    ]
                ]
            ]
        ];
    }
}
