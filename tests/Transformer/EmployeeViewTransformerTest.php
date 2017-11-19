<?php

namespace Tests;

use App\Filter\Filter;
use App\View\View;
use App\View\EmployeeView;
use App\Transformer\EmployeeViewTransformer;
use Laravel\Lumen\Testing\DatabaseTransactions;

class EmployeeViewTransformerTest extends TestCase
{
    use DatabaseTransactions;

    public function testTransformsEmployeeViews()
    {
        $userId = rand(1, 5);
        $companyId = rand(1, 5);
        $name = 'testViewName';
        $mode = EmployeeView::MODE_AND;
        $columns = [
            'first_name',
            'employee_number',
            'last_name'
        ];
        $filters = [
            [
                'field' => 'date_hired',
                'condition' => Filter::GTE,
                'value' => 'value',
            ]
        ];
        $sort = [
            [
                'field' => 'employee_number',
                'order' => View::SORT_ASC,
            ]
        ];
        $pagination = [
            'page' => rand(1, 5),
            'per_page' => rand(10, 20),
        ];
        $view = new EmployeeView([
            'company_id' => $companyId,
            'user_id' => $userId,
            'name' => $name,
            'mode' => $mode,
            'columns' => $columns,
            'filters' => $filters,
            'sort' => $sort,
            'pagination' => $pagination
        ]);

        $expected = [
            'id',
            'company_id',
            'name',
            'mode',
            'columns',
            'filters',
            'sort',
            'pagination'
        ];

        $transformer = new EmployeeViewTransformer();
        $actual = $transformer->transform($view);

        $this->assertEquals($userId, $view->user_id);
        $this->assertEquals($companyId, $view->company_id);
        $this->assertEquals($name, $view->name);
        $this->assertEquals($columns, $view->columns);
        $this->assertEquals($filters, $view->filters);
        $this->assertEquals($sort, $view->sort);
        $this->assertEquals($pagination, $view->pagination);
        $this->assertEquals($mode, $view->mode);
    }
}
