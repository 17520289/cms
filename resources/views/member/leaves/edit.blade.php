<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">

<link rel="stylesheet"
    href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title"><i class="icon-pencil"></i> @lang('app.edit') @lang('app.menu.leaves')</h4>
</div>
<div class="modal-body">
    {!! Form::open(['id' => 'createLeave', 'class' => 'ajax-form', 'method' => 'PUT']) !!}
    <div class="form-body">
        <div class="row">
            {!! Form::hidden('user_id', $user->id) !!}
            {!! Form::hidden('duration', $leave->duration) !!}

            <!--/span-->
        </div>
        <div class="row">

            <div class="col-md-12 ">
                <div class="form-group">
                    <label class="control-label">@lang('modules.leaves.leaveType')</label>
                    <select class="form-control" name="leave_type_id" id="leave_type_id" data-style="form-control">
                        @forelse($leaveTypes as $leaveType)
                            <option @if ($leave->leave_type_id == $leaveType->leaveType->id) selected @endif value="{{ $leaveType->leaveType->id }}">
                                {{ ucwords($leaveType->leaveType->type_name) }}</option>
                        @empty
                            <option value="">@lang('messages.noLeaveTypeAdded')</option>
                        @endforelse
                    </select>
                </div>
            </div>

        </div>
        <!--/row-->

        <div class="row">
            <div class="col-md-6">
                <label>@lang('app.date')</label>
               
                @if ($leave->duration == 'date_range')
                    <label class="control-label">@lang('app.selectDateRange')</label>
                    <div class="form-group">
                        <input class="form-control input-daterange-datepicker" type="text" name="date_range"
                            id="date_range" value="{{ $leave->leave_date->format('m-d-Y') . ' - ' . $endDate->format('m-d-Y') }}" />
                    </div>
                @else
                    <label>@lang('app.date')</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="leave_date" id="single_date"
                            value="{{ $leave->leave_date->format($global->date_format) }}">
                    </div>
                @endif
            </div>

        </div>
        <!--/span-->

        <div class="row">
            <div class="col-xs-12">
                <label>@lang('modules.leaves.reason')</label>
                <div class="form-group">
                    <textarea name="reason" id="reason" class="form-control" cols="30"
                        rows="5">{!! $leave->reason !!}</textarea>
                </div>
            </div>

            {!! Form::hidden('status', $leave->status) !!}

        </div>


    </div>
    {!! Form::close() !!}

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white waves-effect" data-dismiss="modal">@lang('app.close')</button>
    <button type="button" class="btn btn-success save-leave waves-effect waves-light">@lang('app.update')</button>
</div>

<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script>
    $(".select2").select2({
        formatNoMatches: function() {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    jQuery('#single_date').datepicker({
        beforeShowDay: $.datepicker.noWeekends,
        autoclose: true,
        todayHighlight: true,
        weekStart: '{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
        minDate: 0,
        startDate: "-0m",
        endDate: "+1y",
    });

    $('.save-leave').click(function() {
        $.easyAjax({
            url: '{{ route('member.leaves.update', $leave->id) }}',
            container: '#createLeave',
            type: "POST",
            redirect: true,
            data: $('#createLeave').serialize()
        })
    });
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
</script>
