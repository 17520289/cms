<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\BankAccountDataTable;
use App\User;
use Illuminate\Support\Facades\DB;

class BankAccountController extends AdminBaseController
{
    //
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.bankAccountList';
        $this->pageIcon = 'icon-user';
        $this->middleware(function ($request, $next) {
            abort_if(!in_array('employees', $this->user->modules), 403);
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @param BankAccountDataTable $dataTable
     * @return \Illuminate\Http\Response
     * 
     * Edric - 25/8/2021
     */
    public function index(BankAccountDataTable $dataTable)
    {
        $this->employees = User::allEmployees();
        $this->bankNameList = DB::table('bank_accounts')->select('bank_name')->distinct()->where('bank_name', '!=', '')->get();

        return $dataTable->render('admin.bank-account.index', $this->data);
    }
}
