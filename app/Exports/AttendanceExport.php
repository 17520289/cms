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
            
            $final[$employee->name] = array_replace($dataTillToday, $dataFromTomorrow);

            $totalPresent[$employee->name] = 0.0;
            $totalHours[$employee->name] = 0;
            foreach ($employee->attendance as $attendance) {
                $d = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->clock_in_time)->day;
                $jd = gregoriantojd($request->month, $d, $request->year);

                //get total working in day
                $totalWorkingHour = $this->totalHoursRound($attendance);
                $totalHours[$employee->name] += $totalWorkingHour;

                if (jddayofweek($jd, 1) == 'Sunday' || jddayofweek($jd, 1) == 'Saturday') {
                    $final[$employee->name][Carbon::parse($attendance->clock_in_time)->timezone($this->timezone)->day] = $totalWorkingHour . ' - OT';
                } else {
                    $final[$employee->name][Carbon::parse($attendance->clock_in_time)->timezone($this->timezone)->day] = $totalWorkingHour;
                }
            }

            $totalPresent[$employee->name] = round( $totalHours[$employee->name]/8 , 1);
            foreach ($this->holidays as $holiday) {
                if ($final[$employee->name][$holiday->date->day] == 'Absent')
                    $final[$employee->name][$holiday->date->day] = 'Holiday';
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
            'daysInMonth' => $daysInMonth,
            'totalHours' => $totalHours,
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

        $clockInTime1 = Carbon::createFromFormat('H:i:s', $clockInTime->format('H:i:s'));
        $halfday_mark_time = Carbon::createFromFormat( 'H:i:s', $this->attendanceSettings->halfday_mark_time);

        //set clock_out_time if null
        if($attendance->clock_out_time == null ){
            if($clockInTime->isToday()){
                $clockOutTime = Carbon::now();
            }else{
                $clockOutTime = Carbon::createFromFormat('Y-m-d H:i:s' , $clockInTime->format('Y-m-d').' '.$this->attendanceSettings->office_end_time, $this->global->timezone);
            }
        }else{
            $clockOutTime = Carbon::parse($attendance->clock_out_time)->timezone($this->timezone);
        }
       
        //get total hours logged
        $totalWorkingHour = $clockOutTime->floatDiffInHours($clockInTime);
        
         //work from office
         if($attendance->woking_from == 'office'){
            if($clockInTime1->lessThan($halfday_mark_time) && $clockInTime1->greaterThan($halfday_mark_time->subHour())){
                $clockInTime = Carbon::createFromFormat('Y-m-d H:i:s' , $clockInTime->format('Y-m-d').' '.$this->attendanceSettings->halfday_mark_time, $this->timezone);
            }
            $clockInTime1 = Carbon::createFromFormat('H:i:s', $clockInTime->format('H:i:s'));
            $totalWorkingHour = (($totalWorkingHour <= 5) && ($totalWorkingHour >=4)) ? 4 : $totalWorkingHour;
            if($totalWorkingHour > 5){
                $totalWorkingHour -=1;
                if($totalWorkingHour > 8 && $clockInTime1->lessThan($halfday_mark_time->subHour())){
                    $totalWorkingHour = 8;
                }
                if($clockInTime1->greaterThan($halfday_mark_time)){
                    $totalWorkingHour = 4;
                }
            }
        }else{ //work from home
            if($attendance->lunch_break == 'yes'){
                $totalWorkingHour -= 1;
            }
            if($totalWorkingHour > 8){
                $totalWorkingHour = 8;
            }
        }

        if($clockInTime->isToday()){
            $now = Carbon::now();
            if($clockInTime->greaterThan($now)){
                $totalWorkingHour = 0;
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
