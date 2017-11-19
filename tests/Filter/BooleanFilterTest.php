<?php

namespace Tests\Filter;

use App\Filter\BooleanFilter;
use App\Filter\Filter;

class BooleanFilterTest extends \Tests\TestCase
{
    public function testTrueEqualSearchParameter()
    {
        $field = 'field';
        $filter = new BooleanFilter($field, Filter::TRUE);
        $expected = [
            "bool" => [
                "must" => [
                    [
                        "term" => ['field' => true],
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $filter->buildSearchParameters());
    }

    public function testFalseSearchParameter()
    {
        $field = 'field';
        $filter = new BooleanFilter($field, Filter::FALSE);
        $expected = [
            "bool" => [
                "must" => [
                    [
                        "term" => ['field' => false],
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $filter->buildSearchParameters());
    }

    public function testInvalidSearchParameter()
    {
        $field = 'field';
        $filter = new BooleanFilter($field, 'invalid');

        $this->expectException(\InvalidArgumentException::class);
        $filter->buildSearchParameters();
    }
}
