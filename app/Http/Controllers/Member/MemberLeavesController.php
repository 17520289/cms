<?php

namespace App\Http\Controllers\Member;

use App\EmployeeLeaveQuota;
use App\Helper\Reply;
use App\Http\Requests\Leaves\StoreLeave;
use App\Http\Requests\Leaves\UpdateLeave;
use App\Leave;
use App\LeaveType;
use App\Notifications\LeaveApplication;
use App\Notifications\NewLeaveRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MemberLeavesController extends MemberBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.leaves';
        $this->pageIcon = 'icon-logout';
        $this->middleware(function ($request, $next) {
            abort_if(!in_array('leaves', $this->user->modules), 403);
            return $next($request);
        });
    }

    public function index()
    {
        $this->leaves = Leave::byUser($this->user->id);
        $this->leavesCount = Leave::byUserCount($this->user->id);
        $this->leaveTypes = LeaveType::byUser($this->user->id);
        $this->allowedLeaves = $this->user->leaveTypes->sum('no_of_leaves');
        $this->pendingLeaves = Leave::where('status', 'pending')
            ->where('user_id', $this->user->id)
            ->orderBy('leave_date', 'asc')
            ->get();
        $this->employeeLeavesQuota = $this->user->leaveTypes;

        return view('member.leaves.index', $this->data);
    }

    public function create()
    {
        $this->startDate = Carbon::today()->timezone($this->global->timezone)->startOfMonth();
        $this->endDate = Carbon::today()->timezone($this->global->timezone)->endOfMonth();
        $this->leaveTypes = EmployeeLeaveQuota::with('leaveType')
            ->where('no_of_leaves', '>', 0)
            ->where('user_id', $this->user->id)
            ->get();

        $this->leaves = Leave::where('user_id', $this->user->id)
            ->select('leave_date')
            ->where('status', 'approved')
            ->where('duration', '<>', 'half day')
            ->groupBy('leave_date')
            ->get();
        return view('member.leaves.create', $this->data);
    }


    /**
     * Edit function to store new Leave in a date range
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * Edric - 9/1/2020
     */
    public function store(StoreLeave $request)
    {
        if ($request->duration == 'multiple' || $request->duration == 'date_range') {
            if ($request->duration == 'multiple'){
                //session(['leaves_duration' => 'multiple']);
                $dates = explode(',', $request->multi_date);
            }else{
                //session(['leaves_duration' => 'date_range']);
                $t=str_replace(' ', '',$request->date_range);
                $d = explode('-', $t);
                $startDate = Carbon::createFromFormat('m/d/Y', $d[0]);
                $endDate = Carbon::createFromFormat('m/d/Y', $d[1]);
                $dates = $this->getDatesFromRange($startDate, $endDate);
            }
            foreach ($dates as $date) {
                $this->storeLeave($request, $date);
                //session()->forget('leaves_duration');
            }
        } else {
            $this->storeLeave($request, $request->leave_date);
        }
        return Reply::redirect(route('member.leaves.index'), __('messages.leaveAssignSuccess'));
    }

    public function storeLeave($request, $date)
    {
        $leave = new Leave();
        $leave->user_id = $request->user_id;
        $leave->leave_type_id = $request->leave_type_id;
        $leave->duration = $request->duration;
        $leave->leave_date = Carbon::createFromFormat($this->global->date_format, $date)->format('Y-m-d');
        $leave->reason = $request->reason;
        $leave->status = $request->status;
        $leave->save();
    }
    
    /**
     * Get all date between $date1 and $date2
     *
     * @param  $date1, $date2
     * @return array()
     * 
     * Edric - 9/1/2020
     */
    public function getDatesFromRange($date1, $date2, $format = 'Y-m-d')
    {
        $dates = array();
        $current = strtotime($date1);
        $date2 = strtotime($date2);
        $stepVal = '+1 day';
        $i = 0;
        while ($current <= $date2) {
            $dates[$i] = date($format, $current);
            $current = strtotime($stepVal, $current);
            $i++;
        }
        return $dates;
    }
    public function show($id)
    {
        $this->leave = Leave::findOrFail($id);
        return view('member.leaves.show', $this->data);
    }

    public function edit($id)
    {
        $this->leaveTypes = EmployeeLeaveQuota::with('leaveType')
            ->where('no_of_leaves', '>', 0)
            ->where('user_id', $this->user->id)
            ->get();
        $this->leave = Leave::findOrFail($id);
        $view = view('member.leaves.edit', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function update(UpdateLeave $request, $id)
    {
        $leave = Leave::findOrFail($id);
        $leave->user_id = $request->user_id;
        $leave->leave_type_id = $request->leave_type_id;
        $leave->leave_date = Carbon::createFromFormat($this->global->date_format, $request->leave_date)->format('Y-m-d');
        $leave->reason = $request->reason;
        $leave->status = $request->status;
        $leave->save();

        return Reply::redirect(route('member.leaves.index'), __('messages.leaveAssignSuccess'));
    }

    public function destroy($id)
    {
        Leave::destroy($id);
        return Reply::success('messages.leaveDeleteSuccess');
    }

    public function leaveAction(Request $request)
    {
        Leave::destroy($request->leaveId);

        return Reply::success(__('messages.leaveStatusUpdate'));
    }

    public function data()
    {
        $leaves = Leave::with('user', 'type')
            ->where('user_id', $this->user->id)
            ->get();
        return DataTables::of($leaves)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $action = '';

                $action .= '<a href="javascript:;" onclick="getEventDetail(' . $row->id . ')" class="btn btn-info btn-circle"
                      data-toggle="tooltip" data-original-title="View"><i class="fa fa-search" aria-hidden="true"></i></a>';

                if ($row->status == 'pending') {
                    $action .= '  <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-user-id="' . $row->id . '" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
                }

                return $action;
            })
            ->addColumn('type', function ($row) {
                return ucfirst($row->type->type_name);
            })
            ->editColumn('leave_date', function ($row) {
                return Carbon::createFromFormat('Y-m-d H:i:s', $row->leave_date)->format($this->global->date_format);
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 'approved') {
                    return '<label class="label label-success">' . ucfirst($row->status) . '</label>';
                } elseif ($row->status == 'pending') {
                    return '<label class="label label-warning">' . ucfirst($row->status) . '</label>';
                } else {
                    return '<label class="label label-danger">' . ucfirst($row->status) . '</label>';
                }
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }
}
