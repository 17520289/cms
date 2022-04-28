<?php

namespace App\Exports;

use App\User;
use App\Holiday;
use Illuminate\Http\Request;
use Carbon\Carbon as Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Leave;
use App\AttendanceSetting;

class AttendanceExport implements FromView, WithCustomStartCell
{
    private $request;
    private $timezone;
    public function __construct(Request $request, $timezone)
    {
        $this->request = $request;
        $this->timezone = $timezone;
        $this->attendanceSettings = AttendanceSetting::first();
        $this->allowedLeaves = 12;
        $this->startTime = Carbon::createFromFormat('H:i:s',  $this->attendanceSettings->office_start_time , $this->timezone);
        $this->endTime = Carbon::createFromFormat('H:i:s',  $this->attendanceSettings->office_end_time , $this->timezone);
        $this->halfday_mark_time = Carbon::createFromFormat( 'H:i:s', $this->attendanceSettings->halfday_mark_time, $this->timezone);
        $this->lunchBreak = Carbon::createFromFormat('H:i:s' , $this->attendanceSettings->halfday_mark_time, $this->timezone)->subHour();
        
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        //Closure can't take $this->request, so initialize $request
        $request = $this->request;

        $employees = User::with(
            ['attendance' => function ($query) use ($request) {
                $query->whereRaw('MONTH(attendances.clock_in_time) = ?', [$request->month])
                    ->whereRaw('YEAR(attendances.clock_in_time) = ?', [$request->year]);
            }]
        )->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at', 'users.image')
            ->where('roles.name', '<>', 'client')->groupBy('users.id');

        if ($request->user_id == '0') {
            $employees = $employees->get();
        } else {
            $employees = $employees->where('users.id', $request->user_id)->get();
        }

        $this->holidays = Holiday::whereRaw('MONTH(holidays.date) = ?', [$request->month])->whereRaw('YEAR(holidays.date) = ?', [$request->year])->get();

        $final = [];
        $this->daysInMonth = Carbon::parse('01-' . $request->month . '-' . $request->year)->daysInMonth;
        $month = Carbon::parse('01-' . $request->month . '-' . $request->year)->lastOfMonth();
        $now = Carbon::now()->timezone($this->timezone);
        $requestedDate = Carbon::parse(Carbon::parse('01-' . $request->month . '-' . $request->year))->endOfMonth();

        foreach ($employees as $employee) {

            if ($requestedDate->isPast()) {
                $dataTillToday = array_fill(1, $this->daysInMonth, 'Absent');
            } else {
                $dataTillToday = array_fill(1, $now->copy()->format('d'), 'Absent');
            }

            $dataFromTomorrow = [];
            if (($now->copy()->addDay()->format('d') != $this->daysInMonth) && !$requestedDate->isPast()) {
                $dataFromTomorrow = array_fill($now->copy()->addDay()->format('d'), ($this->daysInMonth - $now->copy()->format('d')), '-');
            } else {
                if ($this->daysInMonth < $now->copy()->format('d')) {
                    $dataFromTomorrow = array_fill($month->copy()->addDay()->format('d'), (0), 'Absent');
                } else {
                    $dataFromTomorrow = array_fill($month->copy()->addDay()->format('d'), ($this->daysInMonth - $now->copy()->format('d')), 'Absent');
                }
            }
            
            $final[$employee->name.$employee->id] = array_replace($dataTillToday, $dataFromTomorrow);

            $totalPresent[$employee->name.$employee->id] = 0.0;
            $totalHours[$employee->name.$employee->id] = 0;
            $totalHoursPaid[$employee->name.$employee->id] = 0;
            $totalHoursNoPaid[$employee->name.$employee->id] = 0;
            $leaveTaken[$employee->name.$employee->id] = 0;
            $allLeaveTaken[$employee->name.$employee->id] = 0;
            foreach ($employee->attendance as $attendance) {
                $d = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->clock_in_time)->day;
                $jd = gregoriantojd($request->month, $d, $request->year);

                //get total working in day
                $totalWorkingHour = $this->totalHoursRound($attendance);

                $totalHours[$employee->name.$employee->id] += $totalWorkingHour;

                if (jddayofweek($jd, 1) == 'Sunday' || jddayofweek($jd, 1) == 'Saturday') {
                    $final[$employee->name.$employee->id][Carbon::parse($attendance->clock_in_time)->timezone($this->timezone)->day] = $totalWorkingHour . ' - OT';
                } else {
                    $final[$employee->name.$employee->id][Carbon::parse($attendance->clock_in_time)->timezone($this->timezone)->day] = $totalWorkingHour;
                }
            }


            $totalPresent[$employee->name.$employee->id] = round( $totalHours[$employee->name.$employee->id]/8 , 1);
            foreach ($this->holidays as $holiday) {
                if ($final[$employee->name.$employee->id][$holiday->date->day] == 'Absent')
                    $final[$employee->name.$employee->id][$holiday->date->day] = 'Holiday';
            }
            $leaves = Leave::where('user_id', $employee->id)
                            ->where('status', 'approved')
                            ->whereRaw('MONTH(leaves.leave_date) = ?', [$request->month])
                            ->whereRaw('YEAR(leaves.leave_date) = ?', [$request->year])->get();
            $allLeaveTaken[$employee->name.$employee->id] = Leave::byUserCount($employee->id);     
           
            foreach ($leaves as $leave) {
                $hourOff = $leave->mor_or_aft == 'morning' ? 3 : (($leave->mor_or_aft == 'affternoon') ? 5 : 8);
                $leaveTaken[$employee->name.$employee->id] += $leave->duration == 'half day' ? 0.5 : 1;
                if($this->allowedLeaves - $allLeaveTaken[$employee->name.$employee->id] < 0) {
                    $totalHoursNoPaid[$employee->name.$employee->id] += $hourOff ;
                }else{
                    $totalHoursPaid[$employee->name.$employee->id] += $hourOff ;
                }
                $final[$employee->name.$employee->id][Carbon::parse($leave->leave_date)->timezone($this->timezone)->day] = (8 - $hourOff).'-'.($leave->mor_or_aft == null ? 'full day' : $leave->mor_or_aft ); 
            }

        }

        $daysInMonth = [];
        for ($i = 1; $i <= $this->daysInMonth; $i++) {
            $daysInMonth[] = date('l', strtotime($i . '-' . $request->month . '-' . $request->year));
        }
       
        $this->employeeAttendence = $final;
        $this->totalPresent = $totalPresent;
        return view('admin/reports/attendance/summary_data', [
            'employeeAttendence' => $final,
            'totalPresent' => $totalPresent,
            'totalHoursPaid' => $totalHoursPaid,
            'totalHoursNoPaid' => $totalHoursNoPaid,
            'daysInMonth' => $daysInMonth,
            'leaveTaken' => $leaveTaken,
            'allLeaveTaken' => $allLeaveTaken,
            'totalHours' => $totalHours,
            'allowedLeaves' =>  $this->allowedLeaves,
            'month' => $request->month,
            'year' => $request->year
        ]);
    }

    // public function drawings()
    // {
    //     $drawing = new Drawing();
    //     $drawing->setName('Logo');
    //     $drawing->setDescription('This is my logo');
    //     $drawing->setPath(public_path('/img/visa.jpg'));
    //     $drawing->setHeight(90);
    //     $drawing->setCoordinates('B3');

    //     return $drawing;
    // }

    public function startCell(): string
    {
        return 'A8';
    }
    public function totalHoursRound($attendance)
    { 
        $clockInTime = Carbon::parse($attendance->clock_in_time)->timezone($this->timezone);
        
        $clockInTime1 = Carbon::createFromFormat('H:i:s', $clockInTime->format('H:i:s'), $this->timezone);
      
        $clockOutTime =$clockInTime->isToday() ? Carbon::now() :  Carbon::parse($attendance->clock_out_time)->timezone($this->timezone);
        $clockOutTime1 = Carbon::createFromFormat('H:i:s', $clockOutTime->format('H:i:s'), $this->timezone);

        $leave = Leave::where('user_id', $attendance->user_id)
                        ->where('leave_date',  $clockInTime->format('Y-m-d'))
                        ->where('status' , 'approved')
                        ->first();
        if(@$leave && $leave->mor_or_aft == 'morning' ){
            $startTime = $this->halfday_mark_time;
            $maxHour = 5;
                            
        }else{
            $startTime = $this->startTime;
            $maxHour = ( @$leave && $leave->mor_or_aft == 'affternoon') ? 3 : 8;
        }   

        if($attendance->clock_out_time == null )
        {
            $totalWorkingHour = $clockInTime->isToday() && ! $clockInTime1->greaterThan($startTime) ? $clockOutTime->floatDiffInHours($clockInTime) : 0;
        }
        else
        {
            if($clockOutTime1->greaterThan($this->endTime->copy()->addMinutes($this->attendanceSettings->late_mark_duration))){
                $clockOutTime = Carbon::createFromFormat('Y-m-d H:i:s' ,$clockOutTime->format('Y-m-d'). ' ' . $this->endTime->format('H'). ':'. $this->attendanceSettings->late_mark_duration.':00' , $this->timezone);
            }
            $totalWorkingHour =  $clockOutTime->floatDiffInHours($clockInTime);
           
            $totalWorkingHour = @$leave ? $totalWorkingHour : $totalWorkingHour-1;

            if($clockInTime1->greaterThan( $startTime->copy()->addMinutes($this->attendanceSettings->late_mark_duration)) ){
                $totalWorkingHour = 0;
            }
            
            $totalWorkingHour = $totalWorkingHour > $maxHour ? $maxHour :  $totalWorkingHour;
            
        }
        

        if($attendance->working_from == 'work_from_home')
        {
         
            if($attendance->lunch_break == 'no'){
                $totalWorkingHour += 1;
            }
            if($totalWorkingHour > 8){
                $totalWorkingHour = 8;
            }
        }
        
        $whole = (int) $totalWorkingHour;
        $frac = $totalWorkingHour - $whole;
        if ($frac <= 0.25) {
            $frac = 0;
        } else {
            if ($frac > 0.25 && $frac <= 0.5) {
                $frac = 0.5;
            } else {
                $frac = ($frac <= 0.75) ? 0.5 : 1;
            }
        }

        return $whole + $frac;
        
    }
}
