<?php

namespace Tests\View;

use App\View\EmployeeView;
use App\View\View;
use App\Filter\Filter;
use Laravel\Lumen\Testing\DatabaseTransactions;

class EmployeeViewTest extends \Tests\TestCase
{
    use DatabaseTransactions;

    public function testUserViews()
    {
        $userId = 1;
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
            'company_id' => 1,
            'user_id' => $userId,
            'name' => 'testViewName1',
            'mode' => $mode,
            'columns' => $columns,
            'filters' => $filters,
            'sort' => $sort,
            'pagination' => $pagination
        ]);
        $view->save();
        $view = new EmployeeView([
            'company_id' => 1,
            'user_id' => $userId,
            'name' => 'testViewName2',
            'mode' => $mode,
            'columns' => $columns,
            'filters' => $filters,
            'sort' => $sort,
            'pagination' => $pagination
        ]);
        $view->save();
        $view = new EmployeeView([
            'company_id' => 1,
            'user_id' => 2,
            'name' => 'testViewName3',
            'mode' => $mode,
            'columns' => $columns,
            'filters' => $filters,
            'sort' => $sort,
            'pagination' => $pagination
        ]);
        $view->save();

        $userViews = EmployeeView::userViews($userId);
        $this->assertEquals(2, count($userViews));

        foreach ($userViews as $view) {
            $this->assertInstanceOf(EmployeeView::class, $view);
            $this->assertEquals(1, $view->company_id);
            $this->assertEquals($userId, $view->user_id);
            $this->assertEquals($columns, $view->columns);
            $this->assertEquals($filters, $view->filters);
            $this->assertEquals($sort, $view->sort);
            $this->assertEquals($pagination, $view->pagination);
            $this->assertContains($view->name, [
                'testViewName1',
                'testViewName2',
            ]);
            $this->assertEquals($mode, $view->mode);
        }
    }
}
