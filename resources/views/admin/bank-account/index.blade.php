@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-8 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} </h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-4 col-sm-8 col-md-8 col-xs-12 text-right bg-title-right">
           
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/multiselect/css/multi-select.css') }}">
@endpush

@section('filter-section')
                <div class="row"  id="ticket-filters">
                   
                    <form action="" id="filter-form">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">@lang('app.status')</label>
                                <select class="form-control select2" name="status" id="status" data-style="form-control">
                                    <option value="all">@lang('modules.client.all')</option>
                                    <option value="active">@lang('app.active')</option>
                                    <option value="deactive">@lang('app.inactive')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">@lang('modules.employees.title')</label>
                                <select class="form-control select2" name="employee" id="employee" data-style="form-control">
                                    <option value="all">@lang('modules.client.all')</option>
                                    @forelse($employees as $employee)
                                        <option value="{{$employee->id}}">{{ ucfirst($employee->name) }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">@lang('modules.employees.selectBankName')</label>
                                <select class="form-control select2" name="bankName" id="bankName" data-style="form-control">
                                    <option value="all">@lang('modules.client.all')</option>
                                    @forelse($bankNameList as $bankName)
                                        <option value="{{$bankName->bank_name}}">{{ ucfirst($bankName->bank_name) }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        
                      
                        <div class="col-xs-12">
                            <div class="form-group ">
                                <button type="button" id="apply-filters" class="btn btn-success col-md-6"><i class="fa fa-check"></i> @lang('app.apply')</button>
                                <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                            </div>
                        </div>
                    </form>
                </div>
                @endsection

@section('content')

    <div class="row dashboard-stats">
        <div class="col-md-12 m-b-30">
            <div class="white-box">
               
            </div>
        </div>

    </div>

    <div class="row">
       
        <div class="col-xs-12">
            <div class="white-box">
                
                <div class="table-responsive">
                    {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/multiselect/js/jquery.multi-select.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>

{!! $dataTable->scripts() !!}
<script>

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    var table;

    $(function() {
        loadTable();

        $('body').on('click', '.sa-params', function(){
            var id = $(this).data('user-id');
            swal({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.confirmation.recoverDeleteUser')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('messages.deleteConfirmation')",
                cancelButtonText: "@lang('messages.confirmNoArchive')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('admin.employees.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $('#total-employee').html(`<span class="" >${ response.data.totalEmployees }</span>`);
                                $('#free-employee').html(`<span class="" >${ response.data.freeEmployees }</span>`);
                                $.easyBlockUI('#employees-table');
                                loadTable();
                                $.easyUnblockUI('#employees-table');
                            }
                        }
                    });
                }
            });
        });

        $('body').on('click', '.assign_role', function(){
            var id = $(this).data('user-id');
            var role = $(this).data('role-id');
            var token = "{{ csrf_token() }}";


            $.easyAjax({
                url: '{{route('admin.employees.assignRole')}}',
                type: "POST",
                data: {role: role, userId: id, _token : token},
                success: function (response) {
                    if(response.status == "success"){
                        $.easyBlockUI('#employees-table');
                        loadTable();
                        $.easyUnblockUI('#employees-table');
                    }
                }
            })

        });
    });
    function loadTable(){
        window.LaravelDataTables["employees-table"].draw();
    }

    $('.toggle-filter').click(function () {
        $('#ticket-filters').toggle('slide');
    })

    $('#apply-filters').click(function () {
        $('#employees-table').on('preXhr.dt', function (e, settings, data) {
            var employee = $('#employee').val();
            var status   = $('#status').val();
            var bankName     = $('#bankName').val();
            data['employee'] = employee;
            data['status'] = status;
            data['bankName'] = bankName;
            
        });
        loadTable();
    });

    $('#reset-filters').click(function () {
        $('#filter-form')[0].reset();
        $('#status').val('all');
        $('.select2').val('all');
        $('#filter-form').find('select').select2();
        loadTable();
    })

    function exportData(){

        var employee = $('#employee').val();
        var status   = $('#status').val();
        var role     = $('#role').val();

        var url = '{{ route('admin.employees.export', [':status' ,':employee', ':role']) }}';
        url = url.replace(':role', role);
        url = url.replace(':status', status);
        url = url.replace(':employee', employee);

        window.location.href = url;
    }

</script>
@endpush