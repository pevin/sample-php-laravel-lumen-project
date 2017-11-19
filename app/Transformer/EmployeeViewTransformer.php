<?php

namespace App\Transformer;

use App\View\EmployeeView;
use League\Fractal\TransformerAbstract;

class EmployeeViewTransformer extends TransformerAbstract
{
    /**
     * Transform a EmployeeView model into an array
     *
     * @param EmployeeView $employeeView
     * @return array
     */
    public function transform(EmployeeView $employeeView)
    {
        return [
            'id' => $employeeView->id,
            'name' => $employeeView->name,
            'company_id' => $employeeView->company_id,
            'mode' => $employeeView->mode,
            'columns' => $employeeView->columns,
            'filters' => $employeeView->filters,
            'sort' => $employeeView->sort,
            'pagination' => $employeeView->pagination,
        ];
    }
}
