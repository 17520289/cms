{{-- {{ dd($employeeAttendence) }} --}}

<table class="table">
    <thead>
      <tr>
          <th><strong>Công ty HL Solutions</strong></th>
          <th></th>
      </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>BẢNG CHẤM CÔNG THÁNG {{ $month }}</strong></td>
        </tr>
        <tr>
            <td>Ngày {{ date("1/$month/$year") }}</td>
        </tr>
        <tr>
            <td>Định mức ngày công trong tháng:</td>
        </tr>
    </tbody>
  </table>
@php
    $stt = 0;
@endphp
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th rowspan="2">STT</th>
                <th rowspan="2" style="width:30px">Họ Tên</th>
                @for ($i = 1; $i <= count($daysInMonth); $i++)
                    <th>{{ $i }}</th>
                @endfor
                <th rowspan="2">Giờ công thực tế</th>
                <th  rowspan="2">Giờ nghỉ tính phép</th>
                <th rowspan="2">Giờ nghỉ không tính phép</th>
                <th rowspan="2">Giờ công tính lương</th>
                <th rowspan="2">Số ngày nghỉ trong tháng</th>
                <th rowspan="2">Số ngày nghỉ còn lại</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach($daysInMonth as $key=>$day)
                    <th style="background:#a0a5c7">{{ $day }}</th>
                @endforeach
                <th></th>
            </tr>
            @foreach ($employeeAttendence as $key => $attendance)
            @php
                $stt++;
            @endphp
                <tr>
                    <td>{{ $stt }}</td>
                    <td> {{ $key }} </td>
                    @foreach ($attendance as $key2 => $day)
                        {{-- {{ dd($attendance) }} --}}
                        @if ($key2 <= count($attendance))
                                @if ($day == 'Absent')
                                    @php
                                        $jd = gregoriantojd($month, $key2, $year);
                                    @endphp
                                    @if (jddayofweek($jd, 1) == 'Sunday' || jddayofweek($jd, 1) == 'Saturday')
                                        <td class="text-center" style="background:#daaa82">
                                            {{-- weekend --}}
                                        </td>
                                    @else
                                        @php
                                             $totalHoursNoPaid[$key]+= 8; 
                                        @endphp
                                        <td>0</td>
                                    @endif
                                @elseif($day == 'Holiday')
                                    <td class="text-center" style="background:#f0e49f">8</td>
                                @else
                                @if (strlen($day) > 5)
                                    <td style="background:#f88282">{{ $day }}</td>
                                @else
                                    <td>{{ $day }}</td>
                                @endif
                               
                                @endif
                        @endif
                    @endforeach
                    <td class="text-success">{{ $totalHours[$key] }}</td>
                    <td class="text-success">{{ $totalHoursPaid[$key]  }}</td>
                    <td class="text-success">{{ $totalHoursNoPaid[$key]  }}</td>
                    <td class="text-success">{{ $totalHours[$key] +  $totalHoursPaid[$key] }}</td>
                    <td class="text-success">{{ $leaveTaken[$key] }}</td>
                    <td class="text-success">{{ $allowedLeaves -  $allLeaveTaken[$key] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<table class="table">
    <thead>
      <tr>
          <th>Ghi Chú</th>
          <th></th>
      </tr>
    </thead>
    <tbody>
      <tr>
          <td></td>
          <td>Chấm công theo 2 giá trị là 4 và 8 ( tiếng)</td>
      </tr>
    
    <tr>
        <td></td>
        <td>8 = 1 ngày</td>
    </tr>
    <tr>
        <td  style="background:#f88282"></td>
        <td >Off</td>
    </tr>
    <tr>
        <td style="background:#f0e49f"></td>
        <td>Holiday</td>
    </tr>
    </tbody>
  </table>