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
            <ol class="breadcrumb">
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection
@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/tagify-master/dist/tagify.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-inverse">
                <div class="panel-heading"> @lang('modules.profile.updateTitle')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        {!! Form::open(['id'=>'updateProfile','class'=>'ajax-form','method'=>'PUT']) !!}
                        <div class="form-body">
                            <div class="row">
                                <div class="info-person">
                                    <div class="row">
                                        <div class="panel-heading"> @lang('modules.employees.personalInfo')</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="required">@lang('modules.employees.employeeId')</label>
                                                <a class="mytooltip" href="javascript:void(0)">
                                                    <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span
                                                                    class="tooltip-inner2">@lang('modules.employees.employeeIdInfo')</span></span></span></a>
                                                <input type="text" name="employee_id" id="employee_id" class="form-control"
                                                       autocomplete="nope" readonly value="{{ $employeeDetail->employee_id }}">
                                            </div>
                                        </div>
    
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="required">@lang('modules.employees.employeeName')</label>
                                                <input type="text" name="name" id="name" class="form-control"  value="{{ $userDetail->name }}" autocomplete="nope">
                                            </div>
                                        </div>
    
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="required">@lang('modules.employees.employeeEmail')</label>
                                                <input type="email" name="email" id="email" class="form-control"  value="{{ $userDetail->email }}" autocomplete="nope" >
                                                <span class="help-block">@lang('modules.employees.emailNote')</span>
                                            </div>
                                        </div>
    
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="required">@lang('modules.employees.employeePassword')</label>
                                                <input type="password" style="display: none">
                                                <input type="password" name="password" id="password" class="form-control" autocomplete="nope">
                                                <span class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                                <span class="help-block"> @lang('modules.profile.passwordNote') </span>
                                            </div>
                                        </div>
                                    </div>
                                    <!--/span-->
                                    
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>@lang('app.mobile')</label>
                                                <input type="tel" name="mobile" id="mobile" class="form-control" autocomplete="nope"  value="{{ $userDetail->mobile }}" >
                                            </div>
                                        </div>
    
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label >@lang('modules.employees.date_of_birth')</label>
                                                <input type="text" autocomplete="off"  name="date_of_birth" id="date_of_birth" class="form-control" @if($employeeDetail) value="{{ $employeeDetail->date_of_birth}}"
                                                @endif >
                                            </div>
                                        </div>
    
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>@lang('modules.employees.gender')</label>
                                                <select name="gender" id="gender" class="form-control">
                                                    <option @if($userDetail->gender == 'male') selected
                                                            @endif value="male">@lang('app.male')</option>
                                                    <option @if($userDetail->gender == 'female') selected
                                                            @endif value="female">@lang('app.female')</option>
                                                    <option @if($userDetail->gender == 'others') selected
                                                            @endif value="others">@lang('app.others')</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!--/span-->
                                    
                                    <!--/row-->
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.employees.perAddress')</label>  {{-- Permanent address --}}
                                                 <input autocomplete="nope" type="text" id="permanent_address" name="permanent_address" class="form-control" @if($employeeDetail) value="{{ $employeeDetail->permanent_address}}"
                                                 @endif>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.employees.temResAddress')</label> {{-- Temporary Residence Address --}}
                                                <input autocomplete="nope" type="text" id="temporary_address" name="temporary_address" class="form-control" @if($employeeDetail) value="{{ $employeeDetail->temporary_address}}"
                                                @endif>
                                            </div>
                                        </div>
                                    </div>
                                  
                                    <!--/row-->
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>@lang('modules.employees.identityCardNumber')</label>
                                                    <input autocomplete="nope" type="text" id="id_no" name="id_no" class="form-control" @if($employeeDetail) value="{{ $employeeDetail->id_no}}"
                                                    @endif>
                                            </div>
                                        </div>
                                        <!--/span-->           
    
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>@lang('modules.employees.issueDate')</label>
                                                <input type="text" autocomplete="off"  name="issue_date" id="issue_date" class="form-control" @if($employeeDetail) value="{{ $employeeDetail->issue_date}}"
                                                @endif>
                                            </div>
                                        </div>
                                        <!--/span-->
    
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>@lang('modules.employees.placeOfIssue')</label>
                                                <input type="text" autocomplete="off" name="place_of_issue" id="place_of_issue" class="form-control" @if($employeeDetail) value="{{ $employeeDetail->place_of_issue}}"
                                                @endif>
                                            </div>
                                        </div>
                                    </div>
                                     <!--/row-->
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label"><i class="fa fa-slack"></i> @lang('modules.employees.slackUsername')</label>
                                                <div class="input-group"> <span class="input-group-addon">@</span>
                                                    <input autocomplete="nope" type="text" id="slack_username" name="slack_username" class="form-control"  value="{{ $employeeDetail->slack_username ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <!--/span-->
    
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="required">@lang('modules.employees.joiningDate')</label>
                                                <input type="text" autocomplete="off"  name="joining_date" id="joining_date" class="form-control" readonly @if($employeeDetail) value="{{ $employeeDetail->joining_date->format($global->date_format) }}"
                                                @endif>
                                            </div>
                                        </div>
                                        <!--/span-->
    
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>@lang('modules.employees.lastDate')</label>
                                                <input type="text" autocomplete="off" name="last_date" id="end_date" readonly  @if($employeeDetail) @if($employeeDetail->last_date) value="{{ $employeeDetail->last_date->format($global->date_format) }}" @endif
                                                @endif class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <!--/row-->
    
                                 
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <label>@lang('app.skills')</label>
                                                <input  name='tags' placeholder='@lang('app.skills')'  value='{{implode(' , ', $userDetail->skills()) }}' style="background:white">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <div class="form-group">
                                                <div class="form-group">
                                                    <label>@lang('app.designation') <a href="javascript:;" id="designation-setting" ><i class="ti-settings text-info"></i></a></label>
                                                    <input type="text" class="form-control"  readonly value="{{ $designation->name }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 ">
                                            <div class="form-group">
                                                <label class="required">@lang('app.department') <a href="javascript:;" id="department-setting" ><i class="ti-settings text-info"></i></a></label>
                                                <input type="text" class="form-control"  readonly value="{{ $team->team_name }}">
                                           </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label >@lang('modules.employees.probationnarySalary')  ({{ $global->currency->currency_code }})</label>
                                                <input type="text" name="prob_salary" id="prob_salary" class="form-control" readonly @if($employeeDetail) value="{{ $employeeDetail->prob_salary}}"
                                                @endif>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label >@lang('modules.employees.officePaidSalary')  ({{ $global->currency->currency_code }})</label>
                                                <input type="text" name="office_salary" id="office_salary" class="form-control" readonly @if($employeeDetail) value="{{ $employeeDetail->office_salary}}"
                                                @endif>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          <div class="row">
                            <div class="info-person">
                                <div class="panel-heading"> @lang('modules.employees.bankAccountInfomation') </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label >@lang('modules.employees.accountOwner')  </label>
                                            <input type="text" name="account_owner" id="account_owner" class="form-control" @if ($bankAccount) value="{{ $bankAccount->account_owner }}"
                                            @endif>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label >@lang('modules.employees.accountNumber')  </label>
                                            <input type="text" name="account_number" id="account_number" class="form-control"  @if ($bankAccount) value="{{ $bankAccount->account_number }}"
                                            @endif>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label >@lang('modules.employees.bankName')  </label>
                                            <input type="text" name="bank_name" id="bank_name" class="form-control"  @if ($bankAccount) value="{{ $bankAccount->bank_name }}"
                                            @endif>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label >@lang('modules.employees.branch') </label>
                                            <input type="text" name="branch" id="branch" class="form-control"  @if ($bankAccount) value="{{ $bankAccount->branch }}"
                                            @endif>
                                        </div>
                                    </div>
                                </div>
                            </div>

                          </div>
                            <div class="row">
                                <!--/span-->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="m-b-10">
                                            <label class="control-label">@lang('modules.emailSettings.emailNotifications')</label>
                                        </div>
                                        <div class="radio radio-inline">
                                            <input type="radio" checked name="email_notifications" id="email_notifications1" value="1">
                                            <label for="email_notifications1" class="">
                                                @lang('app.enable') </label>

                                        </div>
                                        <div class="radio radio-inline ">
                                            <input type="radio" name="email_notifications"
                                                   id="email_notifications2" value="0">
                                            <label for="email_notifications2" class="">
                                                @lang('app.disable') </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                           

                            <div class="row">
                                <div class="col-md-6">
                                    <label>@lang('modules.profile.profilePicture')</label>
                                    <div class="form-group">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                <img src="https://via.placeholder.com/200x150.png?text={{ str_replace(' ', '+', __('modules.profile.uploadPicture')) }}"   alt=""/>
                                            </div>
                                            <div class="fileinput-preview fileinput-exists thumbnail"
                                                 style="max-width: 200px; max-height: 150px;"></div>
                                            <div>
                                            <span class="btn btn-info btn-file">
                                            <span class="fileinput-new"> @lang('app.selectImage') </span>
                                            <span class="fileinput-exists"> @lang('app.change') </span>
                                            <input type="file" id="image" name="image"> </span>
                                                <a href="javascript:;" class="btn btn-danger fileinput-exists"
                                                   data-dismiss="fileinput"> @lang('app.remove') </a>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <!--/span-->

                            <div class="row">
                                @if(isset($fields))
                                    @foreach($fields as $field)
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label @if($field->required == 'yes') class="required" @endif>{{ ucfirst($field->label) }}</label>
                                                @if( $field->type == 'text')
                                                    <input type="text" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}">
                                                @elseif($field->type == 'password')
                                                    <input type="password" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}">
                                                @elseif($field->type == 'number')
                                                    <input type="number" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}">

                                                @elseif($field->type == 'textarea')
                                                    <textarea name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" id="{{$field->name}}" cols="3">{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}</textarea>

                                                @elseif($field->type == 'radio')
                                                    <div class="radio-list">
                                                        @foreach($field->values as $key=>$value)
                                                        <label class="radio-inline @if($key == 0) p-0 @endif">
                                                            <div class="radio radio-info">
                                                                <input type="radio" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" id="optionsRadios{{$key.$field->id}}" value="{{$value}}" @if(isset($editUser) && $editUser->custom_fields_data['field_'.$field->id] == $value) checked @elseif($key==0) checked @endif>>
                                                                <label for="optionsRadios{{$key.$field->id}}">{{$value}}</label>
                                                            </div>
                                                        </label>
                                                        @endforeach
                                                    </div>
                                                @elseif($field->type == 'select')
                                                    {!! Form::select('custom_fields_data['.$field->name.'_'.$field->id.']',
                                                            $field->values,
                                                             isset($editUser)?$editUser->custom_fields_data['field_'.$field->id]:'',['class' => 'form-control gender'])
                                                     !!}

                                                @elseif($field->type == 'checkbox')
                                                <div class="mt-checkbox-inline custom-checkbox checkbox-{{$field->id}}">
                                                    <input type="hidden" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" 
                                                    id="{{$field->name.'_'.$field->id}}" value=" ">
                                                    @foreach($field->values as $key => $value)
                                                        <label class="mt-checkbox mt-checkbox-outline">
                                                            <input name="{{$field->name.'_'.$field->id}}[]"
                                                                   type="checkbox" onchange="checkboxChange('checkbox-{{$field->id}}', '{{$field->name.'_'.$field->id}}')" value="{{$value}}"> {{$value}}
                                                            <span></span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                @elseif($field->type == 'date')
                                                    <input type="text" class="form-control date-picker" size="16" name="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                            value="{{ isset($editUser->dob)?Carbon\Carbon::parse($editUser->dob)->format('Y-m-d'):Carbon\Carbon::now()->format($global->date_format)}}">
                                                @endif
                                                <div class="form-control-focus"> </div>
                                                <span class="help-block"></span>

                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" id="save-form" class="btn btn-success"><i class="fa fa-check"></i>
                                @lang('app.update')
                            </button>

                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- .row -->

@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/tagify-master/dist/tagify.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script>
    var input = document.querySelector('input[name=tags]'),
                // init Tagify script on the above inputs
                tagify = new Tagify(input, {
                    whitelist : {!! json_encode($skills) !!},
                    //  blacklist : [".NET", "PHP"] // <-- passed as an attribute in this demo
                });

    $(" #date_of_birth,#issue_date, .date-picker").datepicker({
            todayHighlight: true,
            autoclose: true,
            weekStart:'{{ $global->week_start }}',
            format: '{{ $global->date_picker_format }}',
        });

 $('#save-form').click(function () {
        $.easyAjax({
            url: "{{route('member.profile.update', [$userDetail->id])}}",
            container: '#updateProfile',
            type: "POST",
            redirect: true,
            file: (document.getElementById("image").files.length == 0) ? false : true,
            data: $('#updateProfile').serialize()
        })
    });
</script>
@endpush

