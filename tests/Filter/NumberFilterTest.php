<?php

namespace Tests\Filter;

use App\Filter\NumberFilter;
use App\Filter\Filter;

class NumberFilterTest extends \Tests\TestCase
{
    public function testEqualsSearchParameter()
    {
        $field = 'field';
        $value = 10;
        $filter = new NumberFilter($field, Filter::EQUALS, $value);
        $expected = [
            "bool" => [
                "must" => [
                    [
                        "term" => [
                            $field => $value
                        ],
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $filter->buildSearchParameters());
    }

    public function testGreaterThanEqualSearchParameter()
    {
        $field = 'field';
        $value = 10;
        $filter = new NumberFilter($field, Filter::GTE, $value);
        $expected = [
            "bool" => [
                "must" => [
                    "range" => [
                        $field => [
                            Filter::SEARCH_GTE => $value
                        ]
                    ],
                ]
            ]
        ];

        $this->assertEquals($expected, $filter->buildSearchParameters());
    }

    public function testGreaterThanSearchParameter()
    {
        $field = 'field';
        $value = 10;
        $filter = new NumberFilter($field, Filter::GT, $value);
        $expected = [
            "bool" => [
                "must" => [
                    "range" => [
                        $field => [
                            Filter::SEARCH_GT => $value
                        ]
                    ],
                ]
            ]
        ];

        $this->assertEquals($expected, $filter->buildSearchParameters());
    }

    public function testLessThanEqualSearchParameter()
    {
        $field = 'field';
        $value = 10;
        $filter = new NumberFilter($field, Filter::LTE, $value);
        $expected = [
            "bool" => [
                "must" => [
                    "range" => [
                        $field => [
                            Filter::SEARCH_LTE => $value
                        ]
                    ],
                ]
            ]
        ];

        $this->assertEquals($expected, $filter->buildSearchParameters());
    }

    public function testLessThanSearchParameter()
    {
        $field = 'field';
        $value = 10;
        $filter = new NumberFilter($field, Filter::LT, $value);
        $expected = [
            "bool" => [
                "must" => [
                    "range" => [
                        $field => [
                            Filter::SEARCH_LT => $value
                        ]
                    ],
                ]
            ]
        ];

        $this->assertEquals($expected, $filter->buildSearchParameters());
    }

    public function testInvalidSearchParameter()
    {
        $field = 'field';
        $value = 10;
        $filter = new NumberFilter($field, 'invalid', $value);

        $this->expectException(\InvalidArgumentException::class);
        $filter->buildSearchParameters();
    }
}
