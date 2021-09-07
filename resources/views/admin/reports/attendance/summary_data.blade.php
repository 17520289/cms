{{-- {{ dd($employeeAttendence) }} --}}

<table class="table">
    <thead>
      <tr>
          <th><strong>Công ty HL Solution</strong></th>
          <th></th>
      </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>BẢNG CHẤM CÔNG THÁNG {{ $month }}</strong></td>
        </tr>
        <tr>
            <td>ngày {{ date("d/m/Y") }}</td>
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
                <th>Tổng giờ</th>
                <th>Tổng ngày</th>
                <th>Ghi chú</th>
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
                                        <td>0</td>
                                    @endif
                                @elseif($day == 'Holiday')
                                    <td>holiday</td>
                                @else
                                <td>{{ $day }}</td>
                                @endif
                        @endif
                    @endforeach
                    <td class="text-success">{{ $totalAbsent[$key] }}</td>
                    <td class="text-success">{{ count($daysInMonth)  }}</td>
                    <td class="text-success"></td>
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
        <td>4 = nửa ngày</td>
    </tr>
    <tr>
        <td></td>
        <td>8 = 1 ngày</td>
    </tr>
    </tbody>
  </table>