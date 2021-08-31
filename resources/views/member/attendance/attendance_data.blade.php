<div class="white-box">
    <div class="table-responsive tableFixHead">
        <table class="table table-nowrap mb-0">
            <thead>
                <tr>
                    @for ($i = 1; $i <= $daysInMonth; $i++)
                    <th class="text-center">{{ $i }}</th>
                    @endfor
                    <th>@lang('app.total')</th>
                </tr>
            </thead>
            <tbody>
              {{ $endDate }}
                <tr>
                    @foreach ($attendencesData as $key => $att)
                        @if ($key  <= $daysInMonth)
                        <td class="text-center">
                                @if ($att == 'Absent')
                                    @php
                                        $jd = gregoriantojd($month, $key, $year);
                                    @endphp
                                    @if (jddayofweek($jd, 1) == 'Sunday' || jddayofweek($jd, 1) == 'Saturday')
                                       <i class="fa fa-times text-danger"></i>
                                    @else
                                        <i class="fa fa-smile-o text-danger" aria-hidden="true"></i> 
                                    @endif
                                @elseif($att == 'Holiday')
                                        <i title="holiday"  class="fa fa-flag-o text-warning" ></i></a>
                                @else
                                    {!!  $att  !!}
                                @endif
                            </td>
                         @endif
                        
                    @endforeach
                    <td class="text-success">{{ $totalAbsent . ' / ' . $daysInMonth }}</td>
                </tr>
              
            </tbody>
        </table>
    </div>
</div>
