<?php

namespace App\Filter;

class BooleanFilter extends Filter
{
    const ALLOWED_CONDITIONS = [
        'True' => self::TRUE,
        'False' => self::FALSE,
    ];

    public function buildSearchParameters()
    {
        if (!in_array($this->condition, self::ALLOWED_CONDITIONS)) {
                throw new \InvalidArgumentException('Invalid condition');
        }

        return [

        "bool" => [
            "must" => [
                [
                    "term" => [
                        $this->field => $this->searchCondition()
                    ],
                ]
            ]
        ]

        ];
    }
}
