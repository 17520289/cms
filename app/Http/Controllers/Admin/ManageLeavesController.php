<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\Leaves\StoreLeave;
use App\Http\Requests\Leaves\UpdateLeave;
use App\Leave;
use App\LeaveType;
use App\Notifications\LeaveStatusApprove;
use App\Notifications\LeaveStatusReject;
use App\Notifications\LeaveStatusUpdate;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\GroupLeave;

class ManageLeavesController extends AdminBaseController
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->leaves = Leave::with('user', 'type')->where('status', '<>', 'rejected')
        ->whereYear('leave_date', Carbon::now()->timezone($this->global->timezone)->format('Y'))
        ->get();
        $this->pendingLeaves = Leave::where('status', 'pending')
            ->orderBy('leave_date', 'asc')
            ->get();

        $this->pendingLeavesCount = $this->getTotalPending();
        return view('admin.leaves.index', $this->data);
    }
    public function getTotalPending()
    {
        $pendingLeavesCountOrther = leave::where('status', 'pending')
            ->wherein('duration', ['multiple', 'single', 'half day'])->get();
        $pendingLeavesCountDateRange = Leave::where('status', 'pending')
            ->where('duration', 'date_range')
            ->groupBy('group_leave_id')->get();
        return $pendingLeavesCountOrther->merge($pendingLeavesCountDateRange)->count();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->startDate = Carbon::today()->timezone($this->global->timezone)->startOfMonth();
        $this->endDate = Carbon::today()->timezone($this->global->timezone)->endOfMonth();
        $this->employees = User::allEmployees();
        $this->leaveTypes = LeaveType::all();
        return view('admin.leaves.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(StoreLeave $request)
    {
        $groupId = GroupLeave::create(
            [
                'user_id' => $request->user_id,
                'leave_type_id' => $request->leave_type_id,
                'duration' => $request->duration,
                'status' => $request->status,
            ]
        );
        if ($request->duration == 'multiple' || $request->duration == 'date_range') {
            if ($request->duration == 'multiple') {
                session(['leaves_duration' => 'multiple']);
                $dates = explode(',', $request->multi_date);
            } else {
                session(['leaves_duration' => 'date_range']);
                $t = str_replace(' ', '', $request->date_range);
                $d = explode('-', $t);
                $startDate = Carbon::createFromFormat('m/d/Y', $d[0]);
                $endDate = Carbon::createFromFormat('m/d/Y', $d[1]);
                $dates = $this->getDatesFromRange($startDate, $endDate);
            }
            foreach ($dates as $date) {
                if ($this->checkDateIsWeekends($date) == false) {
                    $this->storeLeave($request, $date, $groupId->id);
                }

                session()->forget('leaves_duration');
            }
        } else {
            if ($this->checkDateIsWeekends($request->leave_date) == false) {
                $this->storeLeave($request, $request->leave_date,  $groupId->id);
            }
        }
        return Reply::redirect(route('admin.leaves.index'), __('messages.leaveAssignSuccess'));
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

    public function storeLeave($request, $date, $GroupLeave)
    {
        $leave = new Leave();
        $leave->user_id = $request->user_id;
        $leave->leave_type_id = $request->leave_type_id;
        $leave->duration = $request->duration;
        $leave->leave_date = Carbon::createFromFormat($this->global->date_format, $date)->format('Y-m-d');
        $leave->group_leave_id = $GroupLeave;
        $leave->reason = $request->reason;
        $leave->status = $request->status;
        $leave->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->leave = Leave::findOrFail($id);
        $this->count = Leave::where('group_leave_id', $this->leave->group_leave_id)->count();
        return view('admin.leaves.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->employees = User::allEmployees();
        $this->leaveTypes = LeaveType::all();
        $this->leave = Leave::findOrFail($id);
        if ($this->leave->duration == 'date_range') {
            $this->endDate = $this->getEndDateOfDateRange($this->leave);
            $this->startDate = Leave::where('group_leave_id', $this->leave->group_leave_id)->first()->leave_date;
        }
        $view = view('admin.leaves.edit', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLeave $request, $id)
    {
        $leave = Leave::findOrFail($id);
        //update table group_leaves
        DB::beginTransaction();
        $GroupLeave =  $leave->group_leave_id;

        if ($leave->duration == 'date_range') {
            $groupId = DB::table('group_leaves')->where('id', $GroupLeave)
                ->update([
                    'user_id' => $request->user_id,
                    'leave_type_id' => $request->leave_type_id,
                    'status' => $request->status,
                ]);
            $test = DB::table('leaves')
                ->where('user_id', $leave->user_id)
                ->where('group_leave_id', $leave->group_leave_id)
                ->delete();

            $t = str_replace(' ', '', $request->date_range);
            $d = explode('-', $t);
            $startDate = Carbon::createFromFormat('m/d/Y', $d[0]);
            $endDate = Carbon::createFromFormat('m/d/Y', $d[1]);
            $dates = $this->getDatesFromRange($startDate, $endDate);

            foreach ($dates as $date) {
                if ($this->checkDateIsWeekends($date) == false) {
                    $this->storeLeave($request, $date, $GroupLeave);
                }
                //session()->forget('leaves_duration');
            }
        } else {
            $leave->user_id = $request->user_id;
            $leave->leave_type_id = $request->leave_type_id;
            $leave->leave_date = Carbon::createFromFormat($this->global->date_format, $request->leave_date)->format('Y-m-d');
            $leave->reason = $request->reason;
            $leave->status = $request->status;
            $leave->save();
        }
        DB::commit();
        $oldStatus = $leave->status;



        return Reply::redirect(route('admin.leaves.index'), __('messages.leaveAssignSuccess'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Leave::destroy($id);
        // return Reply::success('messages.leaveDeleteSuccess');

        $leave = Leave::findOrFail($id);
        if ($leave->duration == 'date_range') {
            DB::table('leaves')->where('group_leave_id', $leave->group_leave_id)->delete();
            GroupLeave::destroy($leave->group_leave_id);
        } else {
            Leave::destroy($id);
            if (Leave::where('group_leave_id', $leave->group_leave_id)->count() == 0) {
                GroupLeave::destroy($leave->group_leave_id);
            }
        }
        return Reply::success('messages.leaveDeleteSuccess');
    }

    /**
     * LeaveAction for pending blade
     * 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * Edric - 9/1/2020
     */
    public function leaveAction(Request $request)
    {
        $leave = Leave::findOrFail($request->leaveId);
        if ($leave->duration == 'date_range') {
            $leaves = DB::table('leaves')
                ->where('group_leave_id', $leave->group_leave_id)
                ->update(['status' => $request->action]);
            if (!empty($request->reason)) {
                $leaves = DB::table('leaves')
                    ->where('group_leave_id', $leave->group_leave_id)
                    ->update(['reject_reason' => $request->reason]);
            }
        } else {
            $leave->status = $request->action;
            if (!empty($request->reason)) {
                $leave->reject_reason = $request->reason;
            }
            $leave->save();
        }

        //upudate status of leave in group_leaves table
        $groupId = DB::table('group_leaves')->where('id', $leave->group_leave_id)
            ->update(['status' => $request->action]);

        return Reply::success(__('messages.leaveStatusUpdate'));
    }

    public function rejectModal(Request $request)
    {
        $this->leaveAction = $request->leave_action;
        $this->leaveID = $request->leave_id;
        return view('admin.leaves.reject-reason-modal', $this->data);
    }

    public function allLeave()
    {
        $this->employees = User::allEmployees();
        $this->fromDate = Carbon::today()->subDays(7);
        $this->toDate = Carbon::today()->addDays(30);
        $this->pendingLeaves = $this->getTotalPending();
        return view('admin.leaves.all-leaves', $this->data);
    }

    public function data(Request $request, $employeeId = null)
    {
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $startDt = '';
        $endDt = '';

        if (!is_null($startDate)) {
            $startDate = Carbon::createFromFormat($this->global->date_format, $startDate)->format('Y-m-d');
            $startDt = 'DATE(leaves.`leave_date`) >= ' . '"' . $startDate . '"';
        }

        if (!is_null($endDate)) {
            $endDate = Carbon::createFromFormat($this->global->date_format, $endDate)->format('Y-m-d');
            $endDt = 'DATE(leaves.`leave_date`) <= ' . '"' . $endDate . '"';
        }

        $leavesListOrther = Leave::select('leaves.id', 'leaves.user_id', 'users.name', 'leaves.leave_date', 'leaves.status', 'leave_types.type_name', 'leave_types.color', 'leaves.duration', 'leaves.group_leave_id')
            ->where('leaves.status', '<>', 'rejected')
            ->whereRaw($startDt)
            ->whereRaw($endDt)
            ->join('users', 'users.id', '=', 'leaves.user_id')
            ->join('leave_types', 'leave_types.id', '=', 'leaves.leave_type_id')
            ->groupBy('leaves.group_leave_id')
            ->where('leaves.duration', 'date_range');

        $leavesDaterange = Leave::select('leaves.id', 'leaves.user_id', 'users.name', 'leaves.leave_date', 'leaves.status', 'leave_types.type_name', 'leave_types.color', 'leaves.duration', 'leaves.group_leave_id')
            ->where('leaves.status', '<>', 'rejected')
            ->whereRaw($startDt)
            ->whereRaw($endDt)
            ->join('users', 'users.id', '=', 'leaves.user_id')
            ->join('leave_types', 'leave_types.id', '=', 'leaves.leave_type_id')
            ->wherein('leaves.duration', ['multiple', 'half day', 'single']);

        if ($employeeId != 0) {
            $leavesListOrther->where('leaves.user_id', $employeeId);
            $leavesDaterange->where('leaves.user_id', $employeeId);
        }
        $leavesList = $leavesDaterange->get()->merge($leavesListOrther->get())->sortBy('leave_date')->all();
        //add enddate for duration date_range
        foreach ($leavesList as $leave) {
            if ($leave->duration == 'date_range') {
                $leave->endDate = $this->getEndDateOfDateRange($leave);
            }
        }


        $leaves = $leavesList;

        return DataTables::of($leaves)
            ->addColumn('employee', function ($row) {
                return ucwords($row->name);
            })
            ->addColumn('date', function ($row) {
                if ($row->duration == 'date_range') {
                    return  $row->leave_date->format('Y-m-d') . " >> " . $row->endDate->format('Y-m-d');
                } else {
                    return $row->leave_date->format('Y-m-d');
                }
            })
            ->addColumn('status', function ($row) {
                $label = $row->status == 'pending' ? 'warning' : 'success';
                return '<div class="label label-' . $label . '">' . $row->status . '</div>';
            })
            ->addColumn('leave_type', function ($row) {
                $type = '<div class="label-' . $row->color . ' label">' . $row->type_name . '</div>';

                if ($row->duration == 'half day') {
                    $type .= ' <div class="label-inverse label">' . __('modules.leaves.halfDay') . '</div>';
                }

                return $type;
            })
            ->addColumn('action', function ($row) {
                if ($row->status == 'pending') {
                    return '<a href="javascript:;"
                            data-leave-id=' . $row->id . ' 
                            data-leave-action="approved" 
                            class="btn btn-success btn-circle leave-action"
                            data-toggle="tooltip"
                            data-original-title="' . __('app.approved') . '">
                                <i class="fa fa-check"></i>
                            </a>
                            <a href="javascript:;" 
                            data-leave-id=' . $row->id . '
                            data-leave-action="rejected"
                            class="btn btn-danger btn-circle leave-action-reject"
                            data-toggle="tooltip"
                            data-original-title="' . __('app.reject') . '">
                                <i class="fa fa-times"></i>
                            </a>
                            
                            <a href="javascript:;"
                            data-leave-id=' . $row->id . '
                            class="btn btn-info btn-circle show-leave"
                            data-toggle="tooltip"
                            data-original-title="' . __('app.details') . '">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </a>';
                }

                return '<a href="javascript:;"
                        data-leave-id=' . $row->id . '
                        class="btn btn-info btn-circle show-leave"
                        data-toggle="tooltip"
                        data-original-title="' . __('app.details') . '">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </a>';
            })
            ->addIndexColumn()
            ->rawColumns(['date', 'status', 'leave_type', 'action'])
            ->make(true);
    }

    /**
     * Edit function to show pending leave
     * 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * Edric - 9/1/2020
     */
    public function pendingLeaves()
    {
        $pendingLeavesOrther = Leave::with('type', 'user', 'user.leaveTypes')->where('status', 'pending')
            ->wherein('duration', ['single', 'multiple', 'half day'])
            ->get();

        $pendingLeavesDateRange = Leave::with('type', 'user', 'user.leaveTypes')->where('status', 'pending')
            ->where('duration', 'date_range')
            ->groupBy('group_leave_id')
            ->get();

        $pendingLeaves1 = $pendingLeavesDateRange->merge($pendingLeavesOrther)->all();
        $this->totalPendingLeave = sizeof($pendingLeaves1);
        foreach ($pendingLeaves1 as $pendingLeave) {
            if ($pendingLeave->duration == 'date_range') {
                $pendingLeave->endDate = $this->getEndDateOfDateRange($pendingLeave);
            }
        }
        $pendingLeaves = collect($pendingLeaves1)->sortBy('leave_date');
        $this->pendingLeaves =   $pendingLeaves->each->append('leaves_taken_count');

        return view('admin.leaves.pending', $this->data);
    }
    /**
     *Get endDate of leave (duration = 'date_range')
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     * 
     * Edric - 9/7/2020
     */
    public function getEndDateOfDateRange($leave)
    {
        $endDate =  Leave::where('group_leave_id', $leave->group_leave_id)->get()->last();
        return  $endDate->leave_date;
    }

    /**
     *Check one date is weekends?
     *
     * @param  \Illuminate\Http\Request  $request
     * @return boolean
     * 
     * Edric - 9/7/2020
     */
    public function checkDateIsWeekends($date)
    {
        $date = explode('-', $date);
        $jd = gregoriantojd($date[1], $date[2], $date[0]);
        return jddayofweek($jd, 1) == 'Sunday' || jddayofweek($jd, 1) == 'Saturday' ? true : false;
    }
}
