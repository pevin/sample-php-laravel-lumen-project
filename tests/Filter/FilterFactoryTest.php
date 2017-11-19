<?php

namespace Tests\Filter;

use App\Filter\FilterFactory;
use App\Filter\KeywordFilter;
use App\Filter\DateFilter;
use App\Filter\NumberFilter;
use App\Filter\BooleanFilter;

class FilterFactoryTest extends \Tests\TestCase
{
    public function testGetFilterClassKeyword()
    {
        $filterClass = FilterFactory::getFilterClass(FilterFactory::TYPE_KEYWORD);
        $this->assertEquals(KeywordFilter::class, $filterClass);
    }

    public function testGetFilterClassDate()
    {
        $filterClass = FilterFactory::getFilterClass(FilterFactory::TYPE_DATE);
        $this->assertEquals(DateFilter::class, $filterClass);
    }

    public function testGetFilterClassNumber()
    {
        $filterClass = FilterFactory::getFilterClass(FilterFactory::TYPE_NUMBER);
        $this->assertEquals(NumberFilter::class, $filterClass);
    }

    public function testGetFilterClassBoolean()
    {
        $filterClass = FilterFactory::getFilterClass(FilterFactory::TYPE_BOOLEAN);
        $this->assertEquals(BooleanFilter::class, $filterClass);
    }

    public function testGetFilterClassInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $filterClass = FilterFactory::getFilterClass('invalid');
    }
}
