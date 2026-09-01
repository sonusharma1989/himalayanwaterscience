<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Hws\FieldService\DataGrids\EmployeeDataGrid;

class EmployeesController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return app(EmployeeDataGrid::class)->toJson();
        }

        return view('hws::admin.employees.index');
    }
}
