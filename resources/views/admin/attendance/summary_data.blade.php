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
                        
                            @if ($key2 +1 <= count($attendance))
                                <td class="text-center">
                                    @if ($day == 'Absent')
                                        @php
                                            $jd = gregoriantojd($month, $key2, $year);
                                        @endphp
                                        @if (jddayofweek($jd, 1) == 'Sunday' || jddayofweek($jd, 1) == 'Saturday')
                                            <a href="javascript:;" class="edit-attendance"
                                                data-attendance-date="{{ $key2 }}"><i
                                                    class="fa fa-times text-danger"></i></a>
                                        @else
                                            <a href="javascript:;" class="edit-attendance"
                                                data-attendance-date="{{ $key2 }}"><i
                                                    class="fa fa-smile-o text-primary" aria-hidden="true"></i></a>
                                        @endif
                                    @elseif($day == 'Holiday')
                                        <a href="javascript:;" title="holiday"
                                            class="edit-attendance" data-attendance-date="{{ $key2 }}"><i
                                                class="fa fa-flag-o text-warning" ></i></a>
                                    @else
                                        {!! $day !!}
                                    @endif
                                </td>
                            @endif
                        @endforeach
                        <td class="text-success">{{ $totalAbsent[$key] . ' / ' . $daysInMonth }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
