@extends('layouts.member-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 bg-title-right">
            <div class="col-md-3 pull-right hidden-xs hidden-sm">

                <select class="selectpicker language-switcher m-t-10  pull-right" data-width="fit">
                    @if ($global->timezone == 'Europe/London')
                        <option value="en" @if ($global->locale == 'en') selected @endif
                            data-content='<span class="flag-icon flag-icon-gb"></span>'>En</option>
                    @else
                        <option value="en" @if ($global->locale == 'en') selected @endif
                            data-content='<span class="flag-icon flag-icon-us"></span>'>En</option>
                    @endif
                    @foreach ($languageSettings as $language)
                        <option value="{{ $language->language_code }}" @if ($user->locale == $language->language_code) selected
                            @endif data-content='<span
                                class="flag-icon @if ($language->language_code == 'zh-CN') flag-icon-cn @elseif($language->language_code == 'zh-TW') flag-icon-tw @else flag-icon-{{ $language->language_code }} @endif"></span>'>{{ $language->language_code }}</option>
                    @endforeach
                </select>

            </div>

            <ol class="breadcrumb">
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
    <style>
        .col-in {
            padding: 0 20px !important;

        }

        .fc-event {
            font-size: 10px !important;
        }

        .front-dashboard .white-box {
            margin-bottom: 8px;
        }

        @media (min-width: 769px) {
            #wrapper .panel-wrapper {
                height: 530px;
                overflow-y: auto;
            }
        }

    </style>
@endpush

@section('content')

    <div class="white-box">
        <div class="row dashboard-stats front-dashboard">
            @if (in_array('projects', $modules))
                <div class="col-md-3 col-sm-6">
                    <a href="{{ route('member.projects.index') }}">
                        <div class="white-box">
                            <div class="row">
                                <div class="col-xs-3">
                                    <div>
                                        <span class="bg-info-gradient"><i class="icon-layers"></i></span>
                                    </div>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <span class="widget-title"> @lang('modules.dashboard.totalProjects')</span><br>
                                    <span class="counter">{{ $totalProjects }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            @if (in_array('timelogs', $modules))
                <div class="col-md-3 col-sm-6">
                    <a href="{{ route('member.all-time-logs.index') }}">
                        <div class="white-box">
                            <div class="row">
                                <div class="col-xs-3">
                                    <div>
                                        <span class="bg-warning-gradient"><i class="icon-clock"></i></span>
                                    </div>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <span class="widget-title"> @lang('modules.dashboard.totalHoursLogged')</span><br>
                                    <span class="counter">{{ $counts->totalHoursLogged }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            @if (in_array('tasks', $modules))
                <div class="col-md-3 col-sm-6">
                    <a href="{{ route('member.all-tasks.index') }}">
                        <div class="white-box">
                            <div class="row">
                                <div class="col-xs-3">
                                    <div>
                                        <span class="bg-danger-gradient"><i class="ti-alert"></i></span>
                                    </div>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <span class="widget-title"> @lang('modules.dashboard.totalPendingTasks')</span><br>
                                    <span class="counter">{{ $counts->totalPendingTasks }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="{{ route('member.all-tasks.index') }}">
                        <div class="white-box">
                            <div class="row">
                                <div class="col-xs-3">
                                    <div>
                                        <span class="bg-success-gradient"><i class="ti-check-box"></i></span>
                                    </div>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <span class="widget-title"> @lang('modules.dashboard.totalCompletedTasks')</span><br>
                                    <span class="counter">{{ $counts->totalCompletedTasks }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

        </div>
        <!-- .row -->

        <div class="row">

            @if (in_array('attendance', $modules))
                <div class="col-md-6">
                    <div class="panel panel-inverse">
                        <div class="panel-heading">@lang('app.menu.attendance')</div>
                        <div class="panel-wrapper collapse in">
                            <div class="panel-body">

                                <input type="hidden" id="current-latitude">
                                <input type="hidden" id="current-longitude">

                                {{-- @if (!isset($noClockIn)) --}}

                                    @if (!$checkTodayHoliday)
                                        @if ($todayTotalClockin < $maxAttandenceInDay)
                                            <div class="col-xs-6">
                                                <h3>@lang('modules.attendance.clock_in')</h3>
                                            </div>
                                            <div class="col-xs-6">
                                                <h3>@lang('modules.attendance.clock_in') IP</h3>
                                            </div>
                                            <div class="col-xs-6">
                                                @if (is_null($currenntClockIn))
                                                    {{ \Carbon\Carbon::now()->timezone($global->timezone)->format($global->time_format) }}
                                                @else
                                                    {{ $currenntClockIn->clock_in_time->timezone($global->timezone)->format($global->time_format) }}
                                                @endif
                                            </div>
                                            <div class="col-xs-6">
                                                {{ $currenntClockIn->clock_in_ip ?? request()->ip() }}
                                            </div>

                                            @if (!is_null($currenntClockIn) && !is_null($currenntClockIn->clock_out_time))
                                                <div class="col-xs-6 m-t-20">
                                                    <label for="">@lang('modules.attendance.clock_out')</label>
                                                    <br>{{ $currenntClockIn->clock_out_time->timezone($global->timezone)->format($global->time_format) }}
                                                </div>
                                                <div class="col-xs-6 m-t-20">
                                                    <label for="">@lang('modules.attendance.clock_out') IP</label>
                                                    <br>{{ $currenntClockIn->clock_out_ip }}
                                                </div>
                                            @endif

                                            <div class="col-xs-8 m-t-20 truncate">
                                                <label for="">Yesterday</label>
                                                <textarea class="form-control" name="yesterday" id="yesterday" cols="50"
                                                    rows="5" @if (!is_null($currenntClockIn)) readonly @endif> @if (!is_null($yesterdayClockIn)){{ $yesterdayClockIn->standUp->todays_Work ?? null }}@endif </textarea>
                                        </div>
                                        <div class="col-xs-8 m-t-20 truncate">
                                            <label for="">Today</label>
                                             <textarea class="form-control" name="today" id="today" cols="50" rows="5"
                                             @if (!is_null($currenntClockIn)) readonly @endif>@if (!is_null($currenntClockIn)){{ $currenntClockIn->standUp->todays_Work ?? null }}@endif </textarea>
                                            </div>
                                            <div class="col-xs-8 m-t-20 truncate">
                                                <label for="">@lang('modules.attendance.working_from'):</label>
                                                @if (is_null($currenntClockIn))
                                                    <select name="working_from" class="form-control" id="working_from">
                                                        <option value="office" selected>@lang('app.office')</option>
                                                        <option value="work_from_home">@lang('app.workFromHome')</option>
                                                    </select>
                                                @else
                                                    @if ($currenntClockIn->working_from == 'work_from_home')
                                                        @lang('app.workFromHome')
                                                    @else
                                                        @lang('app.office')
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="col-xs-8 m-t-20 " id="work-from-home" style="display: none">
                                                <div class="form-group">
                                                    <label>@lang('app.lunchBreak')</label>
                                                    <div class="radio-list">
                                                        <label class="radio-inline p-0">
                                                            <div class="radio radio-info">
                                                                <input type="radio" name="lunch_break" checked value="yes">
                                                                <label>@lang('modules.attendance.yes')</label>
                                                            </div>
                                                        </label>
                                                        <label class="radio-inline">
                                                            <div class="radio radio-info">
                                                                <input type="radio" name="lunch_break" value="no">
                                                                <label for="">@lang('modules.attendance.no')</label>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                               <div class="row">
                                                <div class="col-md-6">
                                                    <div class="input-group bootstrap-timepicker timepicker">
                                                        <label>@lang('modules.attendance.clock_in') </label>
                                                        <input type="text" name="clock_in_time"
                                                            class="form-control a-timepicker" autocomplete="off"
                                                            id="clock-in-time" >
                                                    </div>
                                                </div>
                                             
                                               </div>

                                            </div>
                                            <div class="col-xs-8 m-t-20 ">
                                                @if (!is_null($currenntClockIn) && is_null($currenntClockIn->clock_out_time))
                                                @if ($currenntClockIn->working_from == 'work_from_home')
                                                    <label for="">@lang('app.lunchBreak'):</label>
                                                     @lang('app.'.$currenntClockIn->lunch_break)
                                                    <br><br>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="input-group bootstrap-timepicker timepicker">
                                                                <label>@lang('modules.attendance.clock_out')</label>
                                                                <input type="text" name="clock_out_time" id="clock-out-time"
                                                                        class="form-control b-timepicker" autocomplete="off">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif    
                                                @endif
                                            </div>
                                            <div class="col-xs-4 m-t-20">
                                                <label class="m-t-30">&nbsp;</label>
                                                @if (is_null($currenntClockIn))
                                                    <button class="btn btn-success btn-sm"
                                                        id="clock-in">@lang('modules.attendance.clock_in')</button>
                                                @endif
                                                @if (!is_null($currenntClockIn) && is_null($currenntClockIn->clock_out_time))
                                                    <button class="btn btn-danger btn-sm"
                                                        id="clock-out">@lang('modules.attendance.clock_out')</button>
                                                @endif
                                            </div>
                                        @else
                                            <div class="col-xs-12">
                                                <div class="alert alert-info">@lang('modules.attendance.maxColckIn')</div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="col-xs-12">
                                            <div class="alert alert-info alert-dismissable">
                                                <b>@lang('modules.dashboard.holidayCheck')
                                                    {{ ucwords($checkTodayHoliday->occassion) }}.</b>
                                            </div>
                                        </div>
                                    @endif
                                {{-- @else
                                    <div class="col-xs-12 text-center">
                                        <h4><i class="ti-alert text-danger"></i></h4>
                                        <h4>@lang('messages.officeTimeOver')</h4>
                                    </div>
                                @endif --}}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (in_array('tasks', $modules))
                <div class="col-md-6">
                    <div class="panel panel-inverse">
                        <div class="panel-heading">@lang('modules.dashboard.overdueTasks')</div>
                        <div class="panel-wrapper collapse in">
                            <div class="panel-body">
                                <ul class="list-task list-group" data-role="tasklist">
                                    <li class="list-group-item" data-role="task">
                                        <strong>@lang('app.title')</strong> <span
                                            class="pull-right"><strong>@lang('app.dueDate')</strong></span>
                                    </li>
                                    @forelse($pendingTasks as $key=>$task)
                                        @if ((!is_null($task->project_id) && !is_null($task->project)) || is_null($task->project_id))
                                            <li class="list-group-item row" data-role="task">
                                                <div class="col-xs-8">
                                                    {!! $key + 1 . '. <a href="javascript:;" data-task-id="' . $task->id . '" class="show-task-detail">' . ucfirst($task->heading) . '</a>' !!}
                                                    @if (!is_null($task->project_id) && !is_null($task->project))
                                                        <a href="{{ route('member.projects.show', $task->project_id) }}"
                                                            class="text-danger">{{ ucwords($task->project->project_name) }}</a>
                                                    @endif
                                                </div>
                                                <label
                                                    class="label label-danger pull-right col-xs-4">{{ $task->due_date->format($global->date_format) }}</label>
                                            </li>
                                        @endif
                                    @empty
                                        <li class="list-group-item" data-role="task">
                                            <div class="text-center">
                                                <div class="empty-space" style="height: 200px;">
                                                    <div class="empty-space-inner">
                                                        <div class="icon" style="font-size:20px"><i
                                                                class="fa fa-tasks"></i>
                                                        </div>
                                                        <div class="title m-b-15">@lang("messages.noOpenTasks")
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <div class="row">

            @if (in_array('projects', $modules))
                <div class="col-md-6" id="project-timeline">
                    <div class="panel panel-inverse">
                        <div class="panel-heading">@lang('modules.dashboard.projectActivityTimeline')</div>
                        <div class="panel-wrapper collapse in">
                            <div class="panel-body">
                                <div class="steamline">
                                    @forelse($projectActivities as $activity)
                                        <div class="sl-item">
                                            <div class="sl-left"><i class="fa fa-circle text-info"></i>
                                            </div>
                                            <div class="sl-right">
                                                <div>
                                                    <h6><a href="{{ route('member.projects.show', $activity->project_id) }}"
                                                            class="font-bold">{{ ucwords($activity->project_name) }}:</a>
                                                        {{ $activity->activity }}</h6> <span
                                                        class="sl-date">{{ $activity->created_at->timezone($global->timezone)->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center">
                                            <div class="empty-space" style="height: 200px;">
                                                <div class="empty-space-inner">
                                                    <div class="icon" style="font-size:20px"><i
                                                            class="fa fa-history"></i>
                                                    </div>
                                                    <div class="title m-b-15">@lang("messages.noProjectActivity")
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (in_array('notices', $modules) && $user->cans('view_notice'))
                <div class="col-md-6" id="notices-timeline">
                    <div class="panel panel-inverse">
                        <div class="panel-heading">@lang('modules.module.noticeBoard')</div>
                        <div class="panel-wrapper collapse in">
                            <div class="panel-body">
                                <div class="steamline">
                                    @foreach ($notices as $notice)
                                        <div class="sl-item">
                                            <div class="sl-left"><i class="fa fa-circle text-info"></i>
                                            </div>
                                            <div class="sl-right">
                                                <div>
                                                    <h6>
                                                        <a href="javascript:showNoticeModal({{ $notice->id }});"
                                                            class="text-danger">
                                                            {{ ucwords($notice->heading) }}
                                                        </a>
                                                    </h6>
                                                    <span class="sl-date">
                                                        {{ $notice->created_at->timezone($global->timezone)->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (in_array('employees', $modules))
                <div class="col-md-6">
                    <div class="panel panel-inverse">
                        <div class="panel-heading">@lang('modules.dashboard.userActivityTimeline')</div>
                        <div class="panel-wrapper collapse in">
                            <div class="panel-body">
                                <div class="steamline">
                                    @forelse($userActivities as $key=>$activity)
                                        <div class="sl-item">
                                            <div class="sl-left">
                                                <img src="{{ $activity->user->image_url }}" alt="user" width="30"
                                                    height="30" class="img-circle">'
                                            </div>
                                            <div class="sl-right">
                                                <div class="m-l-40">
                                                    @if ($user->cans('view_employees'))
                                                        <a href="{{ route('member.employees.show', $activity->user_id) }}"
                                                            class="text-success">{{ ucwords($activity->user->name) }}</a>
                                                    @else
                                                        {{ ucwords($activity->user->name) }}
                                                    @endif
                                                    <span
                                                        class="sl-date">{{ $activity->created_at->timezone($global->timezone)->diffForHumans() }}</span>
                                                    <p>{!! ucfirst($activity->activity) !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                        @if (count($userActivities) > $key + 1)
                                            <hr>
                                        @endif
                                    @empty
                                        <div class="text-center">
                                            <div class="empty-space" style="height: 200px;">
                                                <div class="empty-space-inner">
                                                    <div class="icon" style="font-size:20px"><i
                                                            class="fa fa-history"></i>
                                                    </div>
                                                    <div class="title m-b-15">@lang("messages.noActivityByThisUser")
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif



        </div>
    </div>

    {{-- Timer Modal --}}
    <div class="modal fade bs-modal-lg in" id="projectTimerModal" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn blue">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {{-- Timer Modal Ends --}}

    {{-- Ajax Modal --}}
    <div class="modal fade bs-modal-md in" id="subTaskModal" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="subTaskModelHeading">Sub Task e</span>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn blue">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->.
    </div>
    {{-- Ajax Modal Ends --}}
@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>

    <script>
        $('.a-timepicker').timepicker({
            @if ($global->time_format == 'H:i')
                showMeridian: false,
            @endif
            minuteStep: 1
        });
        $('.b-timepicker').timepicker({
            @if ($global->time_format == 'H:i')
                showMeridian: false,
            @endif
            minuteStep: 1,
           
        });
        $("select[name=working_from]").click(function() {
            if ($(this).val() == 'work_from_home') {
                $('#work-from-home').show();
            } else {
                $('#work-from-home').hide();
            }

        })
        $('#clock-in').click(function() {
            var workingFrom = $('#working_from').val();
            var today = $('#today').val();
            var yesterday = $('#yesterday').val();
            var lunchBreak = $("input:radio[name = 'lunch_break']:checked").val();
            var currentLatitude = document.getElementById("current-latitude").value;
            var currentLongitude = document.getElementById("current-longitude").value;
            var clockInTime = $("#clock-in-time").val();
            var token = "{{ csrf_token() }}";

            $.easyAjax({
                url: '{{ route('member.attendances.store') }}',
                type: "POST",
                data: {
                    working_from: workingFrom,
                    today: today,
                    yesterday: yesterday,
                    lunch_break : lunchBreak,
                    clock_in_time : clockInTime,
                    currentLatitude: currentLatitude,
                    currentLongitude: currentLongitude,
                    _token: token
                },
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.reload();
                    }
                }
            })
        })

        @if (!is_null($currenntClockIn))
            $('#clock-out').click(function () {
        
            var token = "{{ csrf_token() }}";
            var currentLatitude = document.getElementById("current-latitude").value;
            var currentLongitude = document.getElementById("current-longitude").value;
            var clockOutTime =  $("#clock-out-time").val();
            $.easyAjax({
            url: '{{ route('member.attendances.update', $currenntClockIn->id) }}',
            type: "PUT",
            data: {
            currentLatitude: currentLatitude,
            currentLongitude: currentLongitude,
            clock_out_time : clockOutTime,
            _token: token
            },
            success: function (response) {
            if(response.status == 'success'){
            window.location.reload();
            }
            }
            })
            })
        @endif

        function showNoticeModal(id) {
            var url = '{{ route('member.notices.show', ':id') }}';
            url = url.replace(':id', id);
            $.ajaxModal('#projectTimerModal', url);
        }

        $('.show-task-detail').click(function() {
            $(".right-sidebar").slideDown(50).addClass("shw-rside");

            var id = $(this).data('task-id');
            var url = "{{ route('member.all-tasks.show', ':id') }}";
            url = url.replace(':id', id);

            $.easyAjax({
                type: 'GET',
                url: url,
                success: function(response) {
                    if (response.status == "success") {
                        $('#right-sidebar-content').html(response.view);
                    }
                }
            });
        })

        $(function() {
            $('.selectpicker').selectpicker();
        });

        $('.language-switcher').change(function() {
            var lang = $(this).val();
            $.easyAjax({
                url: '{{ route('member.language.change-language') }}',
                data: {
                    'lang': lang
                },
                success: function(data) {
                    if (data.status == 'success') {
                        window.location.reload();
                    }
                }
            });
        });
    </script>

    @if ($attendanceSettings->radius_check == 'yes')
        <script>
            var currentLatitude = document.getElementById("current-latitude");
            var currentLongitude = document.getElementById("current-longitude");
            var x = document.getElementById("current-latitude");

            function getLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPosition);
                } else {
                    // x.innerHTML = "Geolocation is not supported by this browser.";
                }
            }

            function showPosition(position) {
                // x.innerHTML = "Latitude: " + position.coords.latitude +
                // "<br>Longitude: " + position.coords.longitude;

                currentLatitude.value = position.coords.latitude;
                currentLongitude.value = position.coords.longitude;
            }
            getLocation();
        </script>
    @endif

@endpush
