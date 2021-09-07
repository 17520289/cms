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

class AttendanceExport implements FromView, WithCustomStartCell
{
    private $request;
    private $timezone;
    public function __construct(Request $request, $timezone)
    {
        $this->request = $request;
        $this->timezone = $timezone;
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

            $totalAbsent[$employee->name] = 0.0;

            foreach ($employee->attendance as $attendance) {
                $d = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->clock_in_time)->day;

                $jd = gregoriantojd($request->month, $d, $request->year);
                if ($attendance->half_day == 'no' || $attendance->half_day == '') {
                    $totalAbsent[$employee->name] += 1;
                } else {
                    $totalAbsent[$employee->name] += 0.5;
                }
                if (jddayofweek($jd, 1) == 'Sunday' || jddayofweek($jd, 1) == 'Saturday') {
                    if ($attendance->half_day == 'yes') {
                        //OT half day
                        $final[$employee->name][Carbon::parse($attendance->clock_in_time)->timezone($this->timezone)->day] = '4';
                    } else {
                        //OT
                        $final[$employee->name][Carbon::parse($attendance->clock_in_time)->timezone($this->timezone)->day] = '8';
                    }
                } else {
                    if ($attendance->half_day == 'no' || $attendance->half_day == '') {
                        // work full day
                        $final[$employee->name][Carbon::parse($attendance->clock_in_time)->timezone($this->timezone)->day] = '8';
                    } else {
                        // work half day
                        $final[$employee->name][Carbon::parse($attendance->clock_in_time)->timezone($this->timezone)->day] = '4';
                    }
                }
            }

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
        $this->totalAbsent = $totalAbsent;
        return view('admin/reports/attendance/summary_data', [
            'employeeAttendence' => $final,
            'totalAbsent' => $totalAbsent,
            'daysInMonth' => $daysInMonth,
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
}
