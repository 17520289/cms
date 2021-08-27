<?php

namespace App\DataTables\Admin;

use App\DataTables\BaseDataTable;
use App\Role;
use App\User;
use App\BankAccount;
use Carbon\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class BankAccountDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $roles = Role::where('name', '<>', 'client')->get();
        $firstAdmin = User::firstAdmin();
        return datatables()
            ->eloquent($query)
            ->addColumn('employeeRole', function ($row) use ($roles, $firstAdmin) {
                return (($row->current_role_name != 'admin') ? $row->current_role_name : __('app.' . $row->roleName));
            })
            ->addColumn('accountOwner', function ($row) {
              
                return $row->account_owner === null ? "-" :  $row->account_owner;
            })
            ->addColumn('accountNumber', function ($row) {
              
                return $row->account_number === null ? "-" :  $row->account_number;
            })
            ->addColumn('bankName', function ($row) {
              
                return $row->bank_name === null ? "-" :  $row->bank_name;
            })
            ->addColumn('branch', function ($row) {
              
                return $row->branch === null ? "-" :  $row->branch;
            })
            ->addColumn('salary', function ($row) {
              
                return $row->office_salary === null ? "-" :  $row->office_salary;
            })
            
            ->addColumn('action', function ($row) {

                $action = '<div class="btn-group dropdown m-r-0">
                 <button aria-expanded="false" data-toggle="dropdown" class="btn btn-default dropdown-toggle waves-effect waves-light" type="button"><i class="fa fa-gears "></i></button>
                    <ul role="menu" class="dropdown-menu pull-right">
                    <li><a href="' . route('admin.employees.edit', [$row->id]) . '"><i class="fa fa-pencil" aria-hidden="true"></i> ' . trans('app.edit') . '</a></li>
                  <li><a href="' . route('admin.employees.show', [$row->id]) . '"><i class="fa fa-search" aria-hidden="true"></i> ' . __('app.view') . '</a></li>';
                $action .= '</ul> </div>';

                return $action;
            })
            ->editColumn(
                'created_at',
                function ($row) {
                    return Carbon::parse($row->created_at)->format($this->global->date_format);
                }
            )
            ->editColumn(
                'status',
                function ($row) {
                    if ($row->status == 'active') {
                        return '<label class="label label-success">' . __('app.active') . '</label>';
                    } else {
                        return '<label class="label label-danger">' . __('app.inactive') . '</label>';
                    }
                }
            )
            ->editColumn('name', function ($row) use ($roles) {

                $image = '<img src="' . $row->image_url . '"alt="user" class="img-circle" width="30" height="30"> ';

                $designation = ($row->designation_name) ? ucwords($row->designation_name) : ' ';

                return  '<div class="row truncate"><div class="col-sm-3 col-xs-4">' . $image . '</div><div class="col-sm-1 col-xs-1"></div><div class="col-sm-7 col-xs-6"><a href="' . route('admin.employees.show', $row->id) . '">' . ucwords($row->name) . '</a><br><span class="text-muted font-12">' . $designation . '</span></div></div>';
            })
            ->addColumn('employee_name', function ($row) use ($roles) {

                return   ucwords($row->name);
            })
            ->addIndexColumn()
            ->rawColumns(['name', 'action',  'status', 'accountOwner','accountNumber','bankName','branch','salary'])
            ->removeColumn('roleId')
            ->removeColumn('roleName');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        $request = $this->request();
      

        $users = User::with('role')
            ->withoutGlobalScope('active')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->leftJoin('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->leftJoin('designations', 'employee_details.designation_id', '=', 'designations.id')
            ->leftJoin('bank_accounts', 'employee_details.user_id', '=', 'bank_accounts.user_id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email','bank_accounts.account_owner','bank_accounts.account_number', 'bank_accounts.bank_name','bank_accounts.branch','employee_details.employee_id','employee_details.office_salary', 'users.created_at', 'roles.name as roleName', 'roles.id as roleId', 'users.image', 'users.status', \DB::raw("(select user_roles.role_id from role_user as user_roles where user_roles.user_id = users.id ORDER BY user_roles.role_id DESC limit 1) as `current_role`"), \DB::raw("(select roles.name from roles as roles where roles.id = current_role limit 1) as `current_role_name`"), 'designations.name as designation_name')
            ->where('roles.name', '<>', 'client');

        if ($request->status != 'all' && $request->status != '') {
            $users = $users->where('users.status', $request->status);
        }

        if ($request->employee != 'all' && $request->employee != '') {
            $users = $users->where('users.id', $request->employee);
        }
        if ($request->bankName != 'all' && $request->bankName != '') {
            $users = $users->where('bank_accounts.bank_name', $request->bankName);
        }


        return $users->groupBy('users.id');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('employees-table')
            ->columns($this->processTitle($this->getColumns()))
            ->minifiedAjax()
            ->dom("<'row'<'col-md-6'l><'col-md-6'Bf>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>")
            ->destroy(true)
            ->orderBy(0)
            ->responsive(true)
            ->serverSide(true)
            ->stateSave(true)
            ->processing(true)
            ->language(__("app.datatable"))
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["employees-table"].buttons().container()
                    .appendTo( ".bg-title .text-right")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
                }',
            ])
            ->buttons(
                Button::make(['extend' => 'export', 'buttons' => ['excel', 'csv'], 'text' => '<i class="fa fa-download"></i> ' . trans('app.exportExcel') . '&nbsp;<span class="caret"></span>'])
            );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            __('app.id') => ['data' => 'id', 'name' => 'id', 'visible' => false, 'exportable' => false],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false],
            __('modules.employees.employeeId') => ['data' => 'employee_id', 'name' => 'employee_details.employee_id'],
            __('app.name') => ['data' => 'name', 'name' => 'name', 'exportable' => false],
            __('app.employee_name') => ['data' => 'employee_name', 'employee_name' => 'employee_name', 'visible' => false],
            __('app.salary') => ['data' => 'salary', 'name' => 'salary'],
            __('modules.employees.accountOwner') => ['data' => 'accountOwner', 'name' => 'accountOwner'],
            __('modules.employees.accountNumber') => ['data' => 'accountNumber', 'name' => 'accountNumber'],
            __('modules.employees.bankName') => ['data' => 'bankName', 'name' => 'bankName'],
            __('modules.employees.branch') => ['data' => 'branch', 'name' => 'branch'],
            __('app.status') => ['data' => 'status', 'name' => 'status'],
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(80)
                ->addClass('text-center')
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'employees_' . date('YmdHis');
    }

    public function pdf()
    {
        set_time_limit(0);
        if ('snappy' == config('datatables-buttons.pdf_generator', 'snappy')) {
            return $this->snappyPdf();
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('datatables::print', ['data' => $this->getDataForPrint()]);

        return $pdf->download($this->getFilename() . '.pdf');
    }
}
