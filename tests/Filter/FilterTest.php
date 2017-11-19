<?php

namespace Tests\Filter;

use App\Filter\Filter;

class FilterTest extends \Tests\TestCase
{
    public function testConstructor()
    {
        $filter = new class ('field', 'cond', 'val') extends Filter {
            public function buildSearchParameters()
            {
            }
        };

        $this->assertEquals('field', $filter->getField());
        $this->assertEquals('cond', $filter->getCondition());
        $this->assertEquals('val', $filter->getValue());
    }

    public function testSearchCondition()
    {
        $filter = new class ('field', 'cond', 'val') extends Filter {
            public function buildSearchParameters()
            {
            }
        };
        $filter->setCondition('invalid');
        $this->expectException(\Exception::class);
        $filter->searchCondition();

        $filter->setCondition(Filter::GTE);
        $this->assertEquals(Filter::SEARCH_GTE, $filter->searchCondition());

        $filter->setCondition(Filter::GT);
        $this->assertEquals(Filter::SEARCH_GT, $filter->searchCondition());

        $filter->setCondition(Filter::LTE);
        $this->assertEquals(Filter::SEARCH_LTE, $filter->searchCondition());

        $filter->setCondition(Filter::LT);
        $this->assertEquals(Filter::SEARCH_LT, $filter->searchCondition());
    }

    public function testGetOptions()
    {
        $expected = [
            [
              'label' => 'True',
              'value' => 'true'
            ],
            [
              'label' => 'False',
              'value' => 'false'
            ]
        ];

        $filter = new class ('field', 'cond', 'val') extends Filter {
            const ALLOWED_CONDITIONS = [
                'True' => self::TRUE,
                'False' => self::FALSE
            ];

            public function buildSearchParameters()
            {
            }
        };

        $this->assertEquals($expected, $filter->getOptions());
    }
}
