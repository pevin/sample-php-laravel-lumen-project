<?php

namespace Tests\Filter;

use App\Filter\KeywordFilter;
use App\Filter\Filter;

class KeywordFilterTest extends \Tests\TestCase
{
    public function testEqualsSearchParameter()
    {
        $field = 'field';
        $value = 10;
        $filter = new KeywordFilter($field, Filter::EQUALS, $value);
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

    public function testContainsSearchParameter()
    {
        $field = 'field';
        $value = 10;
        $filter = new KeywordFilter($field, Filter::PREFIX, $value);
        $expected = [
            "prefix" => [
                $field => [
                    "value" => $value
                ],
            ]
        ];

        $this->assertEquals($expected, $filter->buildSearchParameters());
    }

    public function testInvalidSearchParameter()
    {
        $field = 'field';
        $value = 10;
        $filter = new KeywordFilter($field, 'invalid', $value);

        $this->expectException(\InvalidArgumentException::class);
        $filter->buildSearchParameters();
    }
}
