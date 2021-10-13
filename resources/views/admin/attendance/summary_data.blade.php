<div class="white-box">
    <div class="table-responsive tableFixHead">

        <table class="table table-nowrap mb-0">
            <thead>
                <tr>
                    <th>@lang('app.employee')</th>
                    @for ($i = 1; $i <= $daysInMonth; $i++)
                        <th>{{ $i }}</th>
                    @endfor
                    <th>@lang('app.total')</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($employeeAttendence as $key => $attendance)
                    <tr>

                        <td> {!! end($attendance) !!} </td>

                        @foreach ($attendance as $key2 => $day)

                            @if ($key2 + 1 <= count($attendance))
                                <td class="text-center">
                                    @if ($day == 'Absent')
                                        @php
                                            $jd = gregoriantojd($month, $key2, $year);
                                        @endphp
                                        @if (jddayofweek($jd, 1) == 'Sunday' || jddayofweek($jd, 1) == 'Saturday')
                                            <i title="weekend" class="fa fa-times text-danger"></i>
                                        @else
                                            <a href="javascript:;" class="edit-attendance"
                                                data-attendance-date="{{ $key2 }}"><p>0</p></a>
                                        @endif
                                    @elseif($day == 'Holiday')
                                        <i class="fa fa-flag-o text-warning"></i>
                                    @else
                                        {!! $day !!}
                                    @endif
                                </td>
                            @endif
                        @endforeach
                        <td class="text-success">{{ $totalPresent[$key] . ' / ' . $daysInMonth }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
