<?php

namespace Tests\View;

use App\View\View;
use App\Filter\Filter;
use App\Filter\FilterFactory;
use Basemkhirat\Elasticsearch\Query;
use \Mockery as m;

class ViewTest extends \Tests\TestCase
{
    public function testSetInvalidMode()
    {
        $this->expectException(\InvalidArgumentException::class);
        $view = new class(['mode' => 'test']) extends View {
            protected function getModel()
            {
            }
        };
    }

    public function testSetMode()
    {
        $view = new class(['mode' => View::MODE_AND]) extends View {
            protected function getModel()
            {
            }
        };
        $this->assertEquals(View::MODE_AND, $view->getMode());

        $view = new class(['mode' => View::MODE_OR]) extends View {
            protected function getModel()
            {
            }
        };
        $this->assertEquals(View::MODE_OR, $view->getMode());
    }

    public function testSetInvalidCompanyId()
    {
        $this->expectException(\InvalidArgumentException::class);
        $view = new class(['company_id' => 'test']) extends View {
            protected function getModel()
            {
            }
        };
    }

    public function testSetCompanyId()
    {
        $view = new class(['company_id' => 1234567]) extends View {
            protected function getModel()
            {
            }
        };
        $this->assertEquals(1234567, $view->getCompanyId());
    }

    public function testGetFieldFilterInvalid()
    {
        $view = new class() extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected function getModel()
            {
            }
        };

        $this->expectException(\InvalidArgumentException::class);
        $this->assertEquals(FilterFactory::TYPE_DATE, $view->getFieldFilter('fields'));
    }

    public function testConstructorWithFilters()
    {
        $filters = [
            [
                'field' => 'field',
                'condition' => 'condition',
                'value' => 'value'
            ]
        ];
        $view = new class(
            [
                'mode' => View::MODE_AND,
                'filters' => $filters
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected function getModel()
            {
            }
        };
        $this->assertEquals($filters, $view->filters);
    }

    public function testConstructorWithoutFilters()
    {
        $filters = [];
        $this->expectException(\InvalidArgumentException::class);
        $view = new class(
            [
                'mode' => View::MODE_AND,
                'filters' => $filters
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected function getModel()
            {
            }
        };
    }

    public function testConstructorWithColumns()
    {
        $columns = ['column_1','column_2'];
        $filters = [
            [
                'field' => 'field',
                'condition' => 'condition',
                'value' => 'value'
            ]
        ];
        $view = new class(
            [
                'mode' => View::MODE_AND,
                'columns' => $columns,
                'filters' => $filters,
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected $requiredColumns = ['column_1' => 'Column 1', 'column_2' => 'Column 2'];
            protected function getModel()
            {
            }
        };
        $this->assertEquals($columns, $view->columns);
    }

    public function testConstructorWithoutColumns()
    {
        $filters = [
            [
                'field' => 'field',
                'condition' => 'condition',
                'value' => 'value'
            ]
        ];
        $view = new class(
            [
                'mode' => View::MODE_AND,
                'columns' => [],
                'filters' => $filters,
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected $requiredColumns = ['column_1' => 'Column 1', 'column_2' => 'Column 2'];
            protected function getModel()
            {
            }
        };
        $this->assertEquals(['column_1', 'column_2'], $view->columns);
    }

    public function testColumnsMissingRequired()
    {
        $columns = ['column_1'];
        $filters = [
            [
                'field' => 'field',
                'condition' => 'condition',
                'value' => 'value'
            ]
        ];
        $this->expectException(\InvalidArgumentException::class);
        $view = new class(
            [
                'mode' => View::MODE_AND,
                'columns' => $columns,
                'filters' => $filters,
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected $requiredColumns = ['column_1' => 'Column 1', 'column_2' => 'Column 2'];
            protected function getModel()
            {
            }
        };
    }

    public function testColumnsInvalidColumnsPresent()
    {
        $columns = ['column_1', 'column_2', 'column_3', 'column_4'];
        $filters = [
            [
                'field' => 'field',
                'condition' => 'condition',
                'value' => 'value'
            ]
        ];
        $this->expectException(\InvalidArgumentException::class);
        $view = new class(
            [
                'mode' => View::MODE_AND,
                'columns' => $columns,
                'filters' => $filters,
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected $requiredColumns = ['column_1' => 'Column 1', 'column_2' => 'Column 2'];
            protected $optionalColumns = ['column_3' => 'Column 3'];
            protected function getModel()
            {
            }
        };
    }

    public function testConstructorWithSort()
    {
        $columns = ['column_1','column_2'];
        $filters = [
            [
                'field' => 'field',
                'condition' => 'condition',
                'value' => 'value'
            ]
        ];
        $sort = [
            [
                'field' => 'column_1',
                'order' => View::SORT_ASC,
            ]
        ];
        $view = new class(
            [
                'mode' => View::MODE_AND,
                'columns' => $columns,
                'filters' => $filters,
                'sort' => $sort,
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected $requiredColumns = ['column_1' => 'Column 1', 'column_2' => 'Column 2'];
            protected function getModel()
            {
            }
        };
        $this->assertEquals($sort, $view->sort);
    }

    public function testConstructorWithoutSort()
    {
        $filters = [
            [
                'field' => 'field',
                'condition' => 'condition',
                'value' => 'value'
            ]
        ];
        $defaultSort = [
            [
                'field' => 'column_1',
                'order' => View::SORT_ASC,
            ]
        ];
        $view = new class(
            [
                'mode' => View::MODE_AND,
                'columns' => [],
                'sort' => [],
                'filters' => $filters,
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected $requiredColumns = ['column_1' => 'Column 1', 'column_2' => 'Column 2'];
            protected $defaultSort = [['field' => 'column_1', 'order' => View::SORT_ASC]];
            protected function getModel()
            {
            }
        };
        $this->assertEquals($defaultSort, $view->sort);
    }

    public function testSortMissingOrder()
    {
        $filters = [
            [
                'field' => 'field',
                'condition' => 'condition',
                'value' => 'value'
            ]
        ];
        $sort = [
            [
                'field' => 'column_1',
            ]
        ];
        $this->expectException(\InvalidArgumentException::class);
        $view = new class(
            [
                'mode' => View::MODE_AND,
                'columns' => [],
                'sort' => $sort,
                'filters' => $filters,
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected $requiredColumns = ['column_1' => 'Column 1', 'column_2' => 'Column 2'];
            protected function getModel()
            {
            }
        };
    }

    public function testSortMissingField()
    {
        $filters = [
            [
                'field' => 'field',
                'condition' => 'condition',
                'value' => 'value'
            ]
        ];
        $sort = [
            [
                'order' => 'asc',
            ]
        ];
        $this->expectException(\InvalidArgumentException::class);
        $view = new class(
            [
                'mode' => View::MODE_AND,
                'columns' => [],
                'sort' => $sort,
                'filters' => $filters,
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected $requiredColumns = ['column_1' => 'Column 1', 'column_2' => 'Column 2'];
            protected function getModel()
            {
            }
        };
    }

    public function testSortInvalidSortField()
    {
        $columns = ['column_1', 'column_2'];
        $filters = [
            [
                'field' => 'field',
                'condition' => 'condition',
                'value' => 'value'
            ]
        ];
        $sort = [
            [
                'field' => 'column_1',
                'order' => 'asc',
            ],
            [
                'field' => 'column_2',
                'order' => 'asc',
            ],
            [
                'field' => 'column_3',
                'order' => 'asc',
            ]
        ];
        $this->expectException(\InvalidArgumentException::class);
        $view = new class(
            [
                'mode' => View::MODE_AND,
                'columns' => $columns,
                'sort' => $sort,
                'filters' => $filters,
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected $requiredColumns = ['column_1' => 'Column 1', 'column_2' => 'Column 2'];
            protected function getModel()
            {
            }
        };
    }

    public function testSortInvalidSortOrder()
    {
        $columns = ['column_1', 'column_2'];
        $filters = [
            [
                'field' => 'field',
                'condition' => 'condition',
                'value' => 'value'
            ]
        ];
        $sort = [
            [
                'field' => 'column_1',
                'order' => 'asca',
            ],
        ];
        $this->expectException(\InvalidArgumentException::class);
        $view = new class(
            [
                'mode' => View::MODE_AND,
                'columns' => $columns,
                'sort' => $sort,
                'filters' => $filters,
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected $requiredColumns = ['column_1' => 'Column 1', 'column_2' => 'Column 2'];
            protected function getModel()
            {
            }
        };
    }

    public function testConstructorWithPagination()
    {
        $columns = ['column_1','column_2'];
        $filters = [
            [
                'field' => 'field',
                'condition' => 'condition',
                'value' => 'value'
            ]
        ];
        $sort = [
            [
                'field' => 'column_1',
                'order' => View::SORT_ASC,
            ]
        ];
        $pagination = [
            'page' => rand(1, 5),
            'per_page' => rand(10, 20)
        ];
        $view = new class(
            [
                'mode' => View::MODE_AND,
                'columns' => $columns,
                'filters' => $filters,
                'sort' => $sort,
                'pagination' => $pagination,
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected $requiredColumns = ['column_1' => 'Column 1', 'column_2' => 'Column 2'];
            protected function getModel()
            {
            }
        };
        $this->assertEquals($pagination, $view->pagination);
    }

    public function testConstructorWithoutPagination()
    {
        $filters = [
            [
                'field' => 'field',
                'condition' => 'condition',
                'value' => 'value'
            ]
        ];
        $defaultSort = [
            [
                'field' => 'column_1',
                'order' => View::SORT_ASC,
            ]
        ];
        $view = new class(
            [
                'mode' => View::MODE_AND,
                'columns' => [],
                'sort' => [],
                'pagination' => [],
                'filters' => $filters,
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected $requiredColumns = ['column_1' => 'Column 1', 'column_2' => 'Column 2'];
            protected $defaultSort = [['field' => 'column_1', 'order' => View::SORT_ASC]];
            protected function getModel()
            {
            }
        };
        $this->assertEquals(View::DEFAULT_PER_PAGE, $view->pagination['per_page']);
        $this->assertEquals(View::DEFAULT_PAGE, $view->pagination['page']);
    }

    public function testPaginationMissingPage()
    {
        $columns = ['column_1','column_2'];
        $filters = [
            [
                'field' => 'field',
                'condition' => 'condition',
                'value' => 'value'
            ]
        ];
        $sort = [
            [
                'field' => 'column_1',
                'order' => View::SORT_ASC,
            ]
        ];
        $pagination = [
            'per_page' => rand(10, 20)
        ];
        $this->expectException(\InvalidArgumentException::class);
        $view = new class(
            [
                'mode' => View::MODE_AND,
                'columns' => $columns,
                'filters' => $filters,
                'sort' => $sort,
                'pagination' => $pagination,
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected $requiredColumns = ['column_1' => 'Column 1', 'column_2' => 'Column 2'];
            protected function getModel()
            {
            }
        };
    }

    public function testPaginationMissingPerPage()
    {
        $columns = ['column_1','column_2'];
        $filters = [
            [
                'field' => 'field',
                'condition' => 'condition',
                'value' => 'value'
            ]
        ];
        $sort = [
            [
                'field' => 'column_1',
                'order' => View::SORT_ASC,
            ]
        ];
        $pagination = [
            'page' => rand(10, 20)
        ];
        $this->expectException(\InvalidArgumentException::class);
        $view = new class(
            [
                'mode' => View::MODE_AND,
                'columns' => $columns,
                'filters' => $filters,
                'sort' => $sort,
                'pagination' => $pagination,
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected $requiredColumns = ['column_1' => 'Column 1', 'column_2' => 'Column 2'];
            protected function getModel()
            {
            }
        };
    }

    public function testPaginationPageNotInteger()
    {
        $columns = ['column_1','column_2'];
        $filters = [
            [
                'field' => 'field',
                'condition' => 'condition',
                'value' => 'value'
            ]
        ];
        $sort = [
            [
                'field' => 'column_1',
                'order' => View::SORT_ASC,
            ]
        ];
        $pagination = [
            'page' => 'test',
            'per_page' => 'test'
        ];
        $this->expectException(\InvalidArgumentException::class);
        $view = new class(
            [
                'mode' => View::MODE_AND,
                'columns' => $columns,
                'filters' => $filters,
                'sort' => $sort,
                'pagination' => $pagination,
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected $requiredColumns = ['column_1' => 'Column 1', 'column_2' => 'Column 2'];
            protected function getModel()
            {
            }
        };
    }

    public function testPaginationPageNotGreaterThanZero()
    {
        $columns = ['column_1','column_2'];
        $filters = [
            [
                'field' => 'field',
                'condition' => 'condition',
                'value' => 'value'
            ]
        ];
        $sort = [
            [
                'field' => 'column_1',
                'order' => View::SORT_ASC,
            ]
        ];
        $pagination = [
            'page' => 0,
            'per_page' => -1
        ];
        $this->expectException(\InvalidArgumentException::class);
        $view = new class(
            [
                'mode' => View::MODE_AND,
                'columns' => $columns,
                'filters' => $filters,
                'sort' => $sort,
                'pagination' => $pagination,
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected $requiredColumns = ['column_1' => 'Column 1', 'column_2' => 'Column 2'];
            protected function getModel()
            {
            }
        };
    }

    public function testBuildSearchBodyModeAND()
    {
        $columns = ['column_1','column_2'];
        $filters = [
            [
                'field' => 'field',
                'condition' => Filter::GTE,
                'value' => 'value'
            ]
        ];
        $sort = [
            [
                'field' => 'column_1',
                'order' => 'asc',
            ],
        ];
        $view = new class(
            [
                'mode' => View::MODE_AND,
                'company_id' => 123,
                'columns' => $columns,
                'filters' => $filters,
                'sort' => $sort,
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_NUMBER];
            protected $requiredColumns = ['column_1' => 'Column 1', 'column_2' => 'Column 2'];
            protected function getModel()
            {
            }
        };

        $body = $view->buildSearchBody();
        $this->assertArrayHasKey('query', $body);
        $this->assertArrayHasKey('bool', $body['query']);
        $this->assertArrayHasKey('must', $body['query']['bool']);

        // check if company_id filter is present
        $companyIdFilter = reset($body["query"]["bool"]["must"]);
        $this->assertArrayHasKey('bool', $companyIdFilter);
        $this->assertArrayHasKey('must', $companyIdFilter['bool']);
        $companyIdFilterTerm = current($companyIdFilter['bool']['must']);
        $this->assertArrayHasKey('term', $companyIdFilterTerm);
        $this->assertArrayHasKey('company_id', $companyIdFilterTerm['term']);
        $this->assertEquals(123, $companyIdFilterTerm['term']['company_id']);
    }

    public function testBuildSearchBodyModeOR()
    {
        $columns = ['column_1','column_2'];
        $filters = [
            [
                'field' => 'field',
                'condition' => Filter::GTE,
                'value' => 'value'
            ]
        ];
        $view = new class(
            [
                'mode' => View::MODE_OR,
                'company_id' => 789,
                'columns' => $columns,
                'filters' => $filters,
            ]
        ) extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_NUMBER];
            protected $requiredColumns = ['column_1' => 'Column 1', 'column_2' => 'Column 2'];
            protected function getModel()
            {
            }
        };

        $body = $view->buildSearchBody();
        $this->assertArrayHasKey('query', $body);
        $this->assertArrayHasKey('bool', $body['query']);
        $this->assertArrayHasKey('must', $body['query']['bool']);
        $shouldFilter = end($body['query']['bool']['must']);
        $this->assertArrayHasKey('bool', $shouldFilter);
        $this->assertArrayHasKey('should', $shouldFilter['bool']);
        $this->assertEquals(1, count($shouldFilter['bool']['should']));

        // check if company_id filter is present
        $companyIdFilter = reset($body["query"]["bool"]["must"]);
        $this->assertArrayHasKey('bool', $companyIdFilter);
        $this->assertArrayHasKey('must', $companyIdFilter['bool']);
        $companyIdFilterTerm = current($companyIdFilter['bool']['must']);
        $this->assertArrayHasKey('term', $companyIdFilterTerm);
        $this->assertArrayHasKey('company_id', $companyIdFilterTerm['term']);
        $this->assertEquals(789, $companyIdFilterTerm['term']['company_id']);
    }

    public function testGetFieldFilterMap()
    {
        $expected = [
            [
                'label' => 'Test field one',
                'value' => 'test_field_1',
                'type' => 'keyword'
            ],
            [
                'label' => 'Test field two',
                'value' => 'test_field_2',
                'type' => 'keyword'
            ],
            [
                'label' => 'Test field three',
                'value' => 'test_field_3',
                'type' => 'keyword'
            ],
            [
                'label' => 'Test field four',
                'value' => 'test_field_4',
                'type' => 'keyword'
            ],
            [
                'label' => 'Test field five',
                'value' => 'test_field_5',
                'type' => 'date'
            ],
            [
                'label' => 'Test field six',
                'value' => 'test_field_6',
                'type' => 'date'
            ],
            [
                'label' => 'Test field seven',
                'value' => 'test_field_7',
                'type' => 'number'
            ],
            [
                'label' => 'Test field eight',
                'value' => 'test_field_8',
                'type' => 'boolean'
            ]
        ];

        $view = new class() extends View {
            protected $fieldMap = [
                'test_field_1' => FilterFactory::TYPE_KEYWORD,
                'test_field_2' => FilterFactory::TYPE_KEYWORD,
                'test_field_3' => FilterFactory::TYPE_KEYWORD,
                'test_field_4' => FilterFactory::TYPE_KEYWORD,
                'test_field_5' => FilterFactory::TYPE_DATE,
                'test_field_6' => FilterFactory::TYPE_DATE,
                'test_field_7' => FilterFactory::TYPE_NUMBER,
                'test_field_8' => FilterFactory::TYPE_BOOLEAN
            ];

            protected $requiredColumns = [
                'test_field_2' => "Test field two",
                'test_field_4' => "Test field four",
                'test_field_1' => "Test field one",
            ];

            protected $optionalColumns = [
                'test_field_3' => "Test field three",
                'test_field_5' => "Test field five",
                'test_field_6' => "Test field six",
                'test_field_7' => "Test field seven",
                'test_field_8' => "Test field eight"
            ];

            protected function getModel()
            {
            }
        };

        $this->assertEquals($expected, $view->getFieldFilterMap());
    }

    public function testRun()
    {
        $view = new class() extends View {
            protected $fieldMap = ['field' => FilterFactory::TYPE_DATE];
            protected function getModel()
            {
                $model = m::mock(Model::class);
                $query = m::mock(Query::class);
                $pagination = m::mock(Pagination::class);
                $model->shouldReceive('newQuery')->andReturn($query);
                $query->shouldReceive('body')->andReturn($query);
                $query->shouldReceive('paginate')->andReturn($pagination);
                $pagination->shouldReceive('toArray')->andReturn([]);
                return $model;
            }
        };

        $this->assertEquals([], $view->run());
    }

    public function testGetColumns()
    {
        $expected = [
            [
                'label' => 'First name',
                'value' => 'first_name',
                'required' => true,
            ],
            [
                'label' => 'Last name',
                'value' => 'last_name',
                'required' => true,
            ],
            [
                'label' => 'Employee number',
                'value' => 'employee_number',
                'required' => true,
            ],
            [
                'label' => 'Middle name',
                'value' => 'middle_name',
                'required' => false,
            ],
            [
                'label' => 'Date hired',
                'value' => 'date_hired',
                'required' => false,
            ],
            [
                'label' => 'Date ended',
                'value' => 'date_ended',
                'required' => false,
            ],
            [
                'label' => 'Hours per day',
                'value' => 'hours_per_day',
                'required' => false,
            ],
            [
                'label' => 'Active',
                'value' => 'active',
                'required' => false,
            ],
        ];

        $actual = new class() extends View {
            protected $requiredColumns = [
                'first_name' => "First name",
                'last_name' => "Last name",
                'employee_number' => "Employee number",
            ];

            protected $optionalColumns = [
                'middle_name' => "Middle name",
                'date_hired' => "Date hired",
                'date_ended' => "Date ended",
                'hours_per_day' => "Hours per day",
                'active' => "Active"
            ];

            protected function getModel()
            {
            }
        };

        $this->assertEquals($expected, $actual->getColumns());
    }

    public function testGetModes()
    {
        $expected = [
            [
                'label' => 'Show results that matches ANY filter',
                'value' => 'OR',
            ],
            [
                'label' => 'Show results that matches ALL filters',
                'value' => 'AND',
            ]
        ];

        $actual = new class() extends View {
            public function getModel()
            {
            }
        };

        $this->assertEquals($expected, $actual->getModes());
    }
}
