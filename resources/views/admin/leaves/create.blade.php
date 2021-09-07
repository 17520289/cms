@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 bg-title-right">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.leaves.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.addNew')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-xs-12">

            <div class="panel ">
                <div class="panel-heading"> @lang('modules.leaves.assignLeave')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        {!! Form::open(['id' => 'createLeave', 'class' => 'ajax-form', 'method' => 'POST']) !!}
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-12 ">
                                    <div class="form-group">
                                        <label>@lang('modules.messages.chooseMember')</label>
                                        <select class="select2 form-control"
                                            data-placeholder="@lang('modules.messages.chooseMember')" name="user_id">
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}">{{ ucwords($employee->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!--/span-->
                            </div>
                            <div class="row">

                                <div class="col-md-12 ">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.leaves.leaveType')
                                            <a href="javascript:;" id="addLeaveType"
                                                class="btn btn-sm btn-outline btn-success"><i class="fa fa-plus"></i>
                                                @lang('modules.leaves.addLeaveType')</a>
                                        </label>
                                        <select class="selectpicker form-control" name="leave_type_id" id="leave_type_id"
                                            data-style="form-control">
                                            @forelse($leaveTypes as $leaveType)
                                                <option value="{{ $leaveType->id }}">
                                                    {{ ucwords($leaveType->type_name) }}</option>
                                            @empty
                                                <option value="">@lang('messages.noLeaveTypeAdded')</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>


                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('modules.leaves.selectDuration')</label>
                                        <div class="radio-list">
                                            <label class="radio-inline p-0">
                                                <div class="radio radio-info">
                                                    <input type="radio" name="duration" id="duration_single" checked
                                                        value="single">
                                                    <label for="duration_single">@lang('modules.leaves.single')</label>
                                                </div>
                                            </label>
                                            <label class="radio-inline">
                                                <div class="radio radio-info">
                                                    <input type="radio" name="duration" id="duration_multiple"
                                                        value="multiple">
                                                    <label for="duration_multiple">@lang('modules.leaves.multiple')</label>
                                                </div>
                                            </label>
                                            <label class="radio-inline">
                                                <div class="radio radio-info">
                                                    <input type="radio" name="duration" id="duration_half_day"
                                                        value="half day">
                                                    <label for="duration_half_day">@lang('modules.leaves.halfDay')</label>
                                                </div>
                                            </label>
                                            <label class="radio-inline">
                                                <div class="radio radio-info">
                                                    <input type="radio" name="duration" id="duration_date_range"
                                                        value="date_range">
                                                    <label
                                                        for="duration_date_range">@lang('modules.leaves.dateRange')</label>
                                                </div>
                                            </label>

                                        </div>

                                    </div>
                                </div>

                            </div>
                            <!--/row-->

                            <div class="row">
                                <div class="col-md-6" id="single-date">
                                    <label>@lang('app.date')</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="leave_date" id="single_date"
                                            value="{{ Carbon\Carbon::today()->format($global->date_format) }}">
                                    </div>
                                </div>

                                <div class="col-md-6" id="multi-date" style="display: none">
                                    <label>@lang('modules.leaves.selectDates') <h6>(@lang('messages.selectMultipleDates'))
                                        </h6></label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="multi_date" id="multi_date"
                                            value="{{ Carbon\Carbon::today()->format($global->date_format) }}">
                                    </div>
                                </div>

                                <div class="col-md-6" id="date-range" style="display: none">
                                    <label class="control-label">@lang('app.selectDateRange')</label>
                                    <div class="form-group">
                                        <input class="form-control input-daterange-datepicker" type="text" name="date_range"
                                            id="date_range"
                                            value="{{ $startDate->format('m-d-Y') . ' - ' . $endDate->format('m-d-Y') }}" />
                                    </div>
                                </div>

                            </div>
                            <!--/span-->

                            <div class="row">
                                <div class="col-md-6">
                                    <label>@lang('modules.leaves.reason')</label>
                                    <div class="form-group">
                                        <textarea name="reason" id="reason" class="form-control" cols="30"
                                            rows="5"></textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label>@lang('app.status')</label>
                                    <div class="form-group">
                                        <select class="selectpicker" data-style="form-control" name="status" id="status">
                                            <option value="approved">@lang('app.approved')</option>
                                            <option value="pending">@lang('app.pending')</option>
                                        </select>
                                    </div>
                                </div>

                            </div>


                        </div>
                        <div class="form-actions">
                            <button type="submit" id="save-form-2" class="btn btn-success"><i class="fa fa-check"></i>
                                @lang('app.save')
                            </button>

                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- .row -->

    {{-- Ajax Modal --}}
    <div class="modal fade bs-modal-md in" id="projectCategoryModal" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
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
    {{-- Ajax Modal Ends --}}
@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>



    <script>
        // var startDate = '{{ $startDate->format('Y-m-d') }}';
        // var endDate = '{{ $endDate->format('Y-m-d') }}';
        var nowDate = new Date();
        var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);
        $('.input-daterange-datepicker').daterangepicker({
            minDate: today,
            startDate: Date.now(),
            endDate: @json($endDate->format('m-d-Y')),
            buttonClasses: ['btn', 'btn-sm'],
            cancelClass: 'btn-inverse',
            "locale": {
                "applyLabel": "{{ __('app.apply') }}",
                "cancelLabel": "{{ __('app.cancel') }}",
                "daysOfWeek": [
                    "{{ __('app.su') }}",
                    "{{ __('app.mo') }}",
                    "{{ __('app.tu') }}",
                    "{{ __('app.we') }}",
                    "{{ __('app.th') }}",
                    "{{ __('app.fr') }}",
                    "{{ __('app.sa') }}"
                ],
                "monthNames": [
                    "{{ __('app.january') }}",
                    "{{ __('app.february') }}",
                    "{{ __('app.march') }}",
                    "{{ __('app.april') }}",
                    "{{ __('app.may') }}",
                    "{{ __('app.june') }}",
                    "{{ __('app.july') }}",
                    "{{ __('app.august') }}",
                    "{{ __('app.september') }}",
                    "{{ __('app.october') }}",
                    "{{ __('app.november') }}",
                    "{{ __('app.december') }}",
                ],
                "firstDay": '2021-09-08',
            }
        })


        $('.input-daterange-datepicker').on('apply.daterangepicker', function(ev, picker) {

        });


        $(".select2").select2({
            formatNoMatches: function() {
                return "{{ __('messages.noRecordFound') }}";
            }
        });

        $('#multi_date').datepicker({
            beforeShowDay: $.datepicker.noWeekends,
            multidate: true,
            todayHighlight: true,
            weekStart: '{{ $global->week_start }}',
            format: '{{ $global->date_picker_format }}',
            minDate: 0,
            startDate: "-0m",
            endDate: "+1y",
        });

        $('#single_date').datepicker({
           
            autoclose: true,
            todayHighlight: true,
            weekStart: '{{ $global->week_start }}',
            format: '{{ $global->date_picker_format }}',
            minDate: 0,
            startDate: "-0m",
            endDate: "+1y",
            beforeShowDay: $.datepicker.noWeekends,
        });

        $("input[name=duration]").click(function() {
            if ($(this).val() == 'multiple') {
                $('#multi-date').show();
                $('#single-date').hide();
                $('#date-range').hide();
            } else if ($(this).val() == 'date_range') {
                $('#multi-date').hide();
                $('#single-date').hide();
                $('#date-range').show();
            } else {
                $('#multi-date').hide();
                $('#date-range').hide();
                $('#single-date').show();
            }
        })

        $('#createLeave').on('click', '#addLeaveType', function() {
            var url = '{{ route('admin.leaveType.create') }}';
            $('#modelHeading').html('Manage Leave Type');
            $.ajaxModal('#projectCategoryModal', url);
        })

        $('#save-form-2').click(function() {
            $.easyAjax({
                url: '{{ route('admin.leaves.store') }}',
                container: '#createLeave',
                type: "POST",
                redirect: true,
                data: $('#createLeave').serialize()
            })
        });
    </script>
@endpush
