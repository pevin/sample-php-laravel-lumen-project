<?php

namespace Tests\Filter;

use App\Filter\DateFilter;
use App\Filter\Filter;

class DateFilterTest extends \Tests\TestCase
{
    public function testEqualsSearchParameter()
    {
        $field = 'field';
        $value = date('Y-m-d');
        $filter = new DateFilter($field, Filter::EQUALS, $value);
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
        $value = '10/20/2016';
        $expectedValue = '2016-10-20';
        $filter = new DateFilter($field, Filter::GTE, $value);
        $expected = [
            "bool" => [
                "must" => [
                    "range" => [
                        $field => [
                            Filter::SEARCH_GTE => $expectedValue
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
        $value = '10/20/2016';
        $expectedValue = '2016-10-20';
        $filter = new DateFilter($field, Filter::GT, $value);
        $expected = [
            "bool" => [
                "must" => [
                    "range" => [
                        $field => [
                            Filter::SEARCH_GT => $expectedValue
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
        $value = '10/20/2016';
        $expectedValue = '2016-10-20';
        $filter = new DateFilter($field, Filter::LTE, $value);
        $expected = [
            "bool" => [
                "must" => [
                    "range" => [
                        $field => [
                            Filter::SEARCH_LTE => $expectedValue
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
        $value = '10/20/2016';
        $expectedValue = '2016-10-20';
        $filter = new DateFilter($field, Filter::LT, $value);
        $expected = [
            "bool" => [
                "must" => [
                    "range" => [
                        $field => [
                            Filter::SEARCH_LT => $expectedValue
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
        $value = '10/20/2016';
        $expectedValue = '2016-10-20';
        $filter = new DateFilter($field, 'invalid', $value);

        $this->expectException(\InvalidArgumentException::class);
        $filter->buildSearchParameters();
    }
}
