<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">
<style>
    /* .dropzone .dz-preview .dz-remove {
        display: none;
    } */

    .nonBottomBorder{
        border-bottom: 0;
    }.nonTopBorder{
        border-top: 0;
    }
</style>
<div class="rpanel-title"> @lang('app.task') <span><i class="ti-close right-side-toggle"></i></span> </div>
<div class="r-panel-body p-t-0">

    <div class="row">
        <div class="col-xs-12 col-md-9 p-t-20 b-r h-scroll">
            <div class="col-xs-12">
                <a href="{{route('front.task-share',[$task->hash])}}" target="_blank" data-toggle="tooltip" data-placement="bottom"
                   data-original-title="@lang('app.share')" class="btn btn-default btn-sm m-b-10 btn-rounded btn-outline pull-right m-l-5"> <i class="fa fa-share-alt"></i></a>
                @php $pin = $task->pinned() @endphp
                <a href="javascript:;" class="btn btn-sm btn-info @if(!$pin) btn-outline @endif pull-right m-l-5" data-placement="bottom"  data-toggle="tooltip" data-original-title="@if($pin) @lang('app.unpin') @else @lang('app.pin') @endif"    data-pinned="@if($pin) pinned @else unpinned @endif" id="pinnedItem" >
                    <i class="icon-pin icon-2 pin-icon  @if($pin) pinned @else unpinned @endif" ></i>
                </a>
                <a href="{{route('admin.all-tasks.edit',$task->id)}}" class="btn btn-default btn-sm m-b-10 btn-rounded btn-outline pull-right m-l-5"> <i class="fa fa-edit"></i> @lang('app.edit')</a>

                <a href="javascript:;" id="completedButton" class="btn btn-success btn-sm m-b-10 btn-rounded @if($task->board_column->slug == 'completed') hidden @endif "  onclick="markComplete('completed')" ><i class="fa fa-check"></i> @lang('modules.tasks.markComplete')</a>

                <a href="javascript:;" id="inCompletedButton" class="btn btn-default btn-outline btn-sm m-b-10 btn-rounded @if($task->board_column->slug != 'completed') hidden @endif"  onclick="markComplete('incomplete')"><i class="fa fa-times"></i> @lang('modules.tasks.markIncomplete')</a>

                <a href="javascript:;" id="reminderButton" class="btn btn-default btn-sm m-b-10 btn-rounded btn-outline pull-right @if($task->board_column->slug == 'completed') hidden @endif" title="@lang('messages.remindToAssignedEmployee')"><i class="fa fa-bell"></i> @lang('modules.tasks.reminder')</a>
                @if ($task->board_column->slug != 'completed')

                    @if (count($task->activeTimerAll) > 0)
                        <a href="javascript:;" class="btn btn-default btn-sm m-b-10 btn-rounded btn-outline pull-right  m-l-5 timer-modal"><i class="fa fa-clock-o"></i> @lang('modules.projects.activeTimers')
                            <span class="badge badge-purple">{{ count($task->activeTimerAll) }}</span>
                        </a>
                    @endif
                @endif
            </div>
            <div class="col-xs-12">
                <h4>
                    {{ ucwords($task->heading) }}
                </h4>
                @if(!is_null($task->project_id))
                    <p><i class="icon-layers"></i> {{ ucfirst($task->project->project_name) }}</p>
                @endif

                <h5>
                    @if($task->task_category_id)
                        <label class="label label-default text-dark font-light">{{ ucwords($task->category->category_name) }}</label>
                    @endif

                    <label class="font-light label
                    @if($task->priority == 'high')
                            label-danger
                    @elseif($task->priority == 'medium') label-warning @else label-success @endif
                            ">
                        <span class="text-dark">@lang('modules.tasks.priority') ></span>  {{ ucfirst($task->priority) }}
                    </label>
                </h5>

            </div>

            <ul class="nav customtab nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#home1" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true">@lang('app.task')</a></li>
                <li role="presentation" class=""><a href="#profile1" id = "subtask-count" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false">@lang('modules.tasks.subTask')({{ count($task->subtasks) }})</a></li>
                <li role="presentation" class=""><a href="#messages1" aria-controls="messages" role="tab" data-toggle="tab" aria-expanded="false">@lang('app.file') (<span id="totalUploadedFiles">{{ sizeof($task->files) }}</span>) </a></li>

                <li role="presentation" class=""><a href="#timelogs1" aria-controls="settings" role="tab" data-toggle="tab" aria-expanded="false">@lang('app.menu.timeLogs')</a></li>

                <li role="presentation" class=""><a href="#settings1" aria-controls="settings" role="tab" id = "comment-count" data-toggle="tab" aria-expanded="false">@lang('modules.tasks.comment') ({{ count($task->comments) }})</a></li>
                <li role="presentation" class=""><a href="#notes1" id= "notes-count" aria-controls="note" role="tab" data-toggle="tab" aria-expanded="false">@lang('app.notes') ({{ count($task->notes) }})</a></li>

                <li role="presentation" >  <a href="#history1" id="view-task-history" role="tab" data-toggle="tab" aria-expanded="false" data-task-id="{{ $task->id }}" > <span class="hidden-xs">@lang('modules.tasks.history')</span></a></li>

            </ul>

            <div class="tab-content" id="task-detail-section">
                <div role="tabpanel" class="tab-pane fade active in" id="home1">

                    <div class="col-xs-12" >

                        <div class="row visible-xs visible-sm">
                            <div class="col-xs-6 col-md-3 font-12">
                                <label class="font-12" for="">@lang('modules.tasks.assignTo')</label><br>
                                @foreach ($task->users as $item)
                                    <img src="{{ $item->image_url }}" data-toggle="tooltip"
                                         data-original-title="{{ ucwords($item->name) }}" data-placement="right"
                                         class="img-circle" width="25" height="25" alt="">
                                @endforeach
                            </div>
                            @if($task->create_by)
                                <div class="col-xs-6 col-md-3 font-12">
                                    <label class="font-12" for="">@lang('modules.tasks.assignBy')</label><br>
                                    <img src="{{ $task->create_by->image_url }}" class="img-circle" width="25" height="25" alt="">

                                    {{ ucwords($task->create_by->name) }}
                                </div>
                            @endif

                            @if($task->start_date)
                                <div class="col-xs-6 col-md-3 font-12">
                                    <label class="font-12" for="">@lang('app.startDate')</label><br>
                                    <span class="text-success" >{{ $task->start_date->format($global->date_format) }}</span><br>
                                </div>
                            @endif
                            @if($task->due_date)
                            <div class="col-xs-6 col-md-3 font-12">
                                <label class="font-12" for="">@lang('app.dueDate')</label><br>
                                <span @if($task->due_date->isPast()) class="text-danger" @endif>
                                    {{ $task->due_date->format($global->date_format)  }}
                                </span>
                                <span style="color: {{ $task->board_column->label_color }}" id="columnStatus"> {{ $task->board_column->column_name }}</span>

                            </div>
                            @endif
                        </div>

                        {{--Custom fields data--}}
                        @if(isset($fields) && count($fields) > 0)
                        <div class="row m-t-10">
                            @foreach($fields as $field)
                                <div class="col-md-3">
                                    <label class="font-12" for="">{{ ucfirst($field->label) }}</label><br>
                                    <p class="text-muted">
                                        @if( $field->type == 'text')
                                            {{$task->custom_fields_data['field_'.$field->id] ?? '-'}}
                                        @elseif($field->type == 'password')
                                            {{$task->custom_fields_data['field_'.$field->id] ?? '-'}}
                                        @elseif($field->type == 'number')
                                            {{$task->custom_fields_data['field_'.$field->id] ?? '-'}}

                                        @elseif($field->type == 'textarea')
                                            {{$task->custom_fields_data['field_'.$field->id] ?? '-'}}

                                        @elseif($field->type == 'radio')
                                            {{ !is_null($task->custom_fields_data['field_'.$field->id]) ? $task->custom_fields_data['field_'.$field->id] : '-' }}
                                        @elseif($field->type == 'select')
                                            {{ (!is_null($task->custom_fields_data['field_'.$field->id]) && $task->custom_fields_data['field_'.$field->id] != '') ? $field->values[$task->custom_fields_data['field_'.$field->id]] : '-' }}
                                        @elseif($field->type == 'checkbox')
                                            {{ !is_null($task->custom_fields_data['field_'.$field->id]) ? $field->values[$task->custom_fields_data['field_'.$field->id]] : '-' }}
                                        @elseif($field->type == 'date')
                                            {{ !is_null($task->custom_fields_data['field_'.$field->id]) ? \Carbon\Carbon::parse($task->custom_fields_data['field_'.$field->id])->format($global->date_format) : '--'}}
                                        @endif
                                    </p>

                                </div>
                            @endforeach
                        </div>
                        @endif
                        {{--custom fields data end--}}

                        <div class="row">

                            <div class="col-xs-12 col-md-12 m-t-10">
                                <label class="font-bold" for="">@lang('app.description')</label><br>
                                <div class="task-description m-t-10">
                                    {!! ucfirst($task->description) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="profile1">
                    <div class="col-xs-12 m-b-10">
                        <a href="javascript:;"  data-task-id="{{ $task->id }}" class="add-sub-task"><i class="icon-plus"></i> @lang('app.add') @lang('modules.tasks.subTask')</a>
                    </div>

                    <div class="col-xs-12 m-t-5 ">
                        <h5><i class="ti-check-box"></i> @lang('modules.tasks.subTask')

                                <span class="pull-right" id="percentage-count"><span class="donut" data-peity='{ "fill": ["#00c292", "#eeeeee"],    "innerRadius": 5, "radius": 8 }'>  @if (count($task->subtasks) > 0) {{ count($task->completedSubtasks) }}/{{ count($task->subtasks) }} @else 0/0 @endif</span> <span class="text-muted font-12"> @if (count($task->subtasks)){{ floor((count($task->completedSubtasks)/count($task->subtasks))*100) }}% @else 0% @endif</span></span>


                        </h5>
                        <ul class="list-group b-t" id="sub-task-list">
                            @foreach($task->subtasks as $subtask)
                                @php $subTaskCount = $subtask->files->count(); @endphp
                                <li class="list-group-item row @if($subTaskCount > 0) nonBottomBorder @endif" >
                                    <div class="col-xs-9">
                                        <div class="checkbox checkbox-success checkbox-circle task-checkbox">
                                            <input class="task-check" data-sub-task-id="{{ $subtask->id }}" id="checkbox{{ $subtask->id }}" type="checkbox"
                                                @if($subtask->status == 'complete') checked @endif>
                                            <label for="checkbox{{ $subtask->id }}">&nbsp;</label>
                                            <span>{{ ucfirst($subtask->title) }}</span>
                                        </div>
                                        @if($subtask->due_date)<span class="text-muted m-l-5"> - @lang('modules.invoices.due'): {{ $subtask->due_date->format($global->date_format) }}</span>@endif
                                    </div>

                                    <div class="col-xs-3 text-right">
                                        <a href="javascript:;" data-sub-task-id="{{ $subtask->id }}" title="@lang('app.edit')" class="edit-sub-task"><i class="fa fa-pencil"></i></a>&nbsp;
                                        <a href="javascript:;" data-sub-task-id="{{ $subtask->id }}" title="@lang('app.delete')" class="delete-sub-task"><i class="fa fa-trash"></i></a>
                                    </div>
                                    <div class="row" id="fileList"></div>
                                    @foreach($subtask->files as  $key => $file)
                                        <li class="list-group-item sub-task-file nonTopBorder @if($subTaskCount != ($key+1)) nonBottomBorder @endif" id="sub-task-file-{{  $file->id }}">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    {{ $file->filename }}
                                                </div>
                                                <div class="col-md-3">
                                                    <span class="">{{ $file->created_at->diffForHumans() }}</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <a target="_blank" href="{{ $file->file_url }}"
                                                       data-toggle="tooltip" data-original-title="View"
                                                       class="btn btn-info btn-circle"><i
                                                                class="fa fa-search"></i></a>
                                                    @if(is_null($file->external_link))
                                                        <a href="{{ route('admin.sub-task-files.download', $file->id) }}"
                                                           data-toggle="tooltip" data-original-title="Download"
                                                           class="btn btn-inverse btn-circle"><i
                                                                    class="fa fa-download"></i></a>
                                                    @endif

                                                    <a href="javascript:;" data-toggle="tooltip"  data-original-title="Delete" data-file-id="{{ $file->id }}"
                                                       data-pk="list" class="btn btn-danger btn-circle task-file-delete"><i class="fa fa-times"></i></a>

                                                </div>
                                            </div>
                                        </li>
                                    @endforeach


                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="messages1">
                    <div class="col-xs-12">

                        <button href="javascript:;" id="show-dropzone"
                           class="btn btn-success btn-sm btn-outline  m-b-20"><i class="ti-upload"></i> @lang('modules.projects.uploadFile')</button>

                        <div class="row m-b-20 hide" id="file-dropzone">
                            <div class="col-xs-12">
                                @if($upload)
                                    <form action="{{ route('admin.task-files.store') }}" class="dropzone"
                                          id="file-upload-dropzone">
                                        {{ csrf_field() }}

                                        {!! Form::hidden('task_id', $task->id) !!}

                                        <input name="view" type="hidden" id="view" value="list">

                                        <div class="fallback">
                                            <input name="file" type="file" multiple/>
                                        </div>
                                    </form>

                                @else
                                    <div class="alert alert-danger">@lang('messages.storageLimitExceed', ['here' => '<a href='.route('admin.billing.packages'). '>Here</a>'])</div>
                                @endif
                            </div>
                        </div>

                        <ul class="list-group" id="files-list">
                            @forelse($task->files as $file)
                            <li class="list-group-item" id="task-file-{{  $file->id }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        {{ $file->filename }}
                                    </div>
                                    <div class="col-md-3">
                                        <span class="">{{ $file->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="col-md-3">
                                            <a target="_blank" href="{{ $file->file_url }}"
                                               data-toggle="tooltip" data-original-title="View"
                                               class="btn btn-info btn-circle"><i
                                                        class="fa fa-search"></i></a>
                                        @if(is_null($file->external_link))
                                        <a href="{{ route('admin.task-files.download', $file->id) }}"
                                           data-toggle="tooltip" data-original-title="Download"
                                           class="btn btn-inverse btn-circle"><i
                                                    class="fa fa-download"></i></a>
                                        @endif

                                        <a href="javascript:;" data-toggle="tooltip" data-original-title="Delete" data-file-id="{{ $file->id }}"
                                           data-pk="list" class="btn btn-danger btn-circle file-delete"><i class="fa fa-times"></i></a>

                                    </div>
                                </div>
                            </li>
                            @empty
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-md-10">
                                            @lang('messages.noFileUploaded')
                                        </div>
                                    </div>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="timelogs1">

                    <div class="col-xs-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>@lang('app.employee')</th>
                                    <th>@lang('modules.employees.hoursLogged')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($employees as $item)
                                <tr>
                                    <td>
                                        <img src="{{ $item->image_url }}" width="25" height="25" class="img-circle">
                                        <span class="font-semi-bold">{{ ucwords($item->name) }}</span><br>
                                    </td>
                                    <td>
                                        @php
                                            $timeLog = intdiv($item->total_minutes, 60) . ' ' . __('app.hrs') . ' ';

                                            if (($item->total_minutes % 60) > 0) {
                                                $timeLog .= ($item->total_minutes % 60) . ' ' . __('app.mins');
                                            }
                                        @endphp
                                        {{ $timeLog }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2">
                                        @lang('messages.noRecordFound')
                                    </td>

                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>


                </div>
                <div role="tabpanel" class="tab-pane" id="settings1">
                    <div class="col-xs-12">
                        <h4>@lang('modules.tasks.comment')</h4>
                    </div>

                    <div class="col-xs-12" id="comment-container">
                        <div id="comment-list">
                            @forelse($task->comments as $comment)

                                <div class="row b-b m-b-5 font-12">
                                    <div class="col-xs-12 m-b-5">
                                        <span class="font-semi-bold">{{ ucwords($comment->user->name) }}</span> <span class="text-muted font-12">{{ ucfirst($comment->created_at->diffForHumans()) }}</span>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="row m-b-10">
                                            <div class="col-md-6">{!! ucfirst($comment->comment)  !!}</div>
                                            <div class="col-md-6 text-right">
                                                <a href="javascript:;" data-comment-id="{{ $comment->id }}" class="btn btn-xs  btn-outline btn-default" onclick="deleteComment('{{ $comment->id }}');return false;"><i class="fa fa-trash"></i> @lang('app.delete')</a>
                                            </div>
                                        </div>
                                        @if(!is_null($comment->comment_file))
                                        @foreach ($comment->comment_file as $file )
                                        <li class="list-group-item m-t-10" style="border-top: none;" id="task-comment-file-{{  $file->id }}">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    {{ $file->filename }}
                                                </div>
                                                 <div class="col-md-3 text-right">
                                                        <a target="_blank" href="{{ $file->file_url }}"
                                                           data-toggle="tooltip" data-original-title="View"
                                                           class="btn btn-info btn-circle"><i
                                                                    class="fa fa-search"></i></a>
                                                    @if(is_null($file->external_link))
                                                    <a href="{{ route('admin.task-comment.download', $file->id) }}"
                                                       data-toggle="tooltip" data-original-title="Download"
                                                       class="btn btn-inverse btn-circle"><i
                                                                class="fa fa-download"></i></a>
                                                    @endif

                                                    <a href="javascript:;" data-toggle="tooltip" data-original-title="Delete" data-file-id="{{ $file->id }}"
                                                       data-pk="list" class="btn btn-danger btn-circle comment-file-delete"><i class="fa fa-times"></i></a>

                                                 </div>
                                            </div>
                                        </li>
                                        @endforeach

                                        @endif
                                    </div>

                                </div>
                            @empty
                                <div class="col-xs-12">
                                    @lang('messages.noCommentFound')
                                </div>
                            @endforelse
                        </div>

                    </div>

                    <div class="form-group" id="comment-box">


                    <div class="col-xs-12">
                        <textarea name="comment" id="task-comment" class="summernote" placeholder="@lang('modules.tasks.comment')"></textarea>
                        <button href="javascript:;" id="show-dropzone-comment"
                        class="btn btn-success btn-sm btn-outline  m-b-20"><i class="ti-upload"></i> @lang('modules.projects.uploadImage')</button>
                        <div class="row m-b-20 hide" id="file-dropzone-comment">
                            <div class="col-xs-12">
                                @if($upload)
                                    <div class="dropzone"
                                        id="file-upload-dropzone-comment">
                                        {{ csrf_field() }}
                                        <div class="fallback">
                                            <input name="file" type="file" multiple/>
                                        </div>
                                        <input name="image_url" id="image_url"type="hidden" />
                                    </div>
                                    <input type="hidden" name="taskID" id="taskID">
                                    <input type="hidden" name="commentID" id="commentID">
                                @else
                                    <div class="alert alert-danger">@lang('messages.storageLimitExceed', ['here' => '<a href='.route('admin.billing.packages'). '>Here</a>'])</div>
                                @endif
                            </div>
                        </div>
                    </div>
                        <div class="col-xs-12">
                            <a href="javascript:;" id="submit-comment" class="btn btn-info btn-sm"><i class="fa fa-send"></i> @lang('app.submit')</a>
                        </div>
                    </div>

                </div>
                <div role="tabpanel" class="tab-pane" id="notes1">
                    <div class="col-xs-12">
                        <h4>@lang('app.notes')</h4>
                    </div>

                    <div class="col-xs-12" id="note-container">
                        <div id="note-list">
                            @forelse($task->notes as $note)
                                <div class="row b-b m-b-5 font-12">
                                    <div class="col-xs-12">
                                        <h5>{{ ucwords($note->user->name) }} <span class="text-muted font-12">{{ ucfirst($note->created_at->diffForHumans()) }}</span></h5>
                                    </div>
                                    <div class="col-xs-10">
                                        {!! ucfirst($note->note)  !!}
                                    </div>
                                    <div class="col-xs-2 text-right">
                                        <a href="javascript:;" data-comment-id="{{ $note->id }}" class="btn btn-xs  btn-outline btn-default" onclick="deleteNote('{{ $note->id }}');return false;"><i class="fa fa-trash"></i> @lang('app.delete')</a>
                                    </div>
                                </div>
                            @empty
                                <div class="col-xs-12">
                                    @lang('messages.noNoteFound')
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="form-group" id="note-box">
                        <div class="col-xs-12">
                            <textarea name="note" id="task-note" class="summernote" placeholder="@lang('app.notes')"></textarea>
                        </div>
                        <div class="col-xs-12">
                            <a href="javascript:;" id="submit-note" class="btn btn-info btn-sm"><i class="fa fa-send"></i> @lang('app.submit')</a>
                        </div>
                    </div>


                </div>
                <div role="tabpanel" class="tab-pane" id="history1">
                    <div class="col-xs-12">
                        <label class="font-bold">@lang('modules.tasks.history')</label>
                    </div>
                    <div class="col-xs-12" id="task-history-section">
                    </div>
                </div>
            </div>



        </div>

        <div class="col-xs-6 col-md-3 hidden-xs hidden-sm">

            <div class="row">
                <div class="col-xs-12 p-10 p-t-20 ">
                    <label class="font-12" for="">@lang('app.status')</label><br>
                    <span id="columnStatusColor" style="width: 15px; height: 15px; background-color: {{ $task->board_column->label_color }}" class="btn btn-small btn-circle">&nbsp;</span> <span id="columnStatus">{{ $task->board_column->column_name }}</span>
                </div>

                <div class="col-xs-12">
                    <hr>

                    <label class="font-12" for="">@lang('modules.tasks.assignTo')</label><br>
                    @foreach ($task->users as $item)
                        <img src="{{ $item->image_url }}" data-toggle="tooltip"
                             data-original-title="{{ ucwords($item->name) }}" data-placement="right"
                             class="img-circle" width="35" height="35" alt="">
                    @endforeach
                    <hr>
                </div>
                @if($task->create_by)
                    <div class="col-xs-12">
                        <label class="font-12" for="">@lang('modules.tasks.assignBy')</label><br>
                        <img src="{{ $task->create_by->image_url }}" class="img-circle" width="35" height="35" alt="">

                        {{ ucwords($task->create_by->name) }}
                        <hr>
                    </div>
                @endif

                @if($task->start_date)
                    <div class="col-xs-12  ">
                        <label class="font-12" for="">@lang('app.startDate')</label><br>
                        <span class="text-success" >{{ $task->start_date->format($global->date_format) }}</span><br>
                        <hr>
                    </div>
                @endif
                @if($task->due_date)
                    <div class="col-xs-12 ">
                        <label class="font-12" for="">@lang('app.dueDate')</label><br>
                        <span @if($task->due_date->isPast()) class="text-danger" @endif>
                            {{ $task->due_date->format($global->date_format) }}
                        </span>
                        <hr>
                    </div>
             @endif

                @if ($task->estimate_hours > 0 || $task->estimate_minutes > 0)
                    <div class="col-xs-12 ">
                        <label class="font-12" for="">@lang('app.estimate')</label><br>

                        <span>
                            {{ $task->estimate_hours }} @lang('app.hrs')
                            {{ $task->estimate_minutes }} @lang('app.mins')
                        </span>
                        <hr>
                    </div>

                    <div class="col-xs-12 ">
                        <label class="font-12" for="">@lang('modules.employees.hoursLogged')</label><br>
                        <span>
                            @php
                                $timeLog = intdiv($task->timeLogged->sum('total_minutes'), 60) . ' ' . __('app.hrs') . ' ';

                                if (($task->timeLogged->sum('total_minutes') % 60) > 0) {
                                    $timeLog .= ($task->timeLogged->sum('total_minutes') % 60) . ' ' . __('app.mins');
                                }
                            @endphp
                            <span @if ($task->total_estimated_minutes < $task->timeLogged->sum('total_minutes')) class="text-danger font-semi-bold" @endif>
                                {{ $timeLog }}
                            </span>
                        </span>
                        <hr>
                    </div>
                @else
                    <div class="col-xs-12 ">
                        <label class="font-12" for="">@lang('modules.employees.hoursLogged')</label><br>
                        <span>
                            @php
                                $timeLog = intdiv($task->timeLogged->sum('total_minutes'), 60) . ' ' . __('app.hrs') . ' ';

                                if (($task->timeLogged->sum('total_minutes') % 60) > 0) {
                                    $timeLog .= ($task->timeLogged->sum('total_minutes') % 60) . ' ' . __('app.mins');
                                }
                            @endphp
                            <span>
                                {{ $timeLog }}
                            </span>
                        </span>
                        <hr>
                    </div>
                @endif

                @if(sizeof($task->label))
                    <div class="col-xs-12">
                        <label class="font-12" for="">@lang('app.label')</label><br>
                        <span>
                            @foreach($task->label as $key => $label)
                                <label class="badge text-capitalize font-semi-bold" style="background:{{ $label->label->label_color }}">{{ ucwords($label->label->label_name) }} </label>
                            @endforeach
                        </span>
                        <hr>
                    </div>
                @endif

            </div>


        </div>



    </div>

</div>

<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
{{-- <script src="{{ asset('js/sweet-alert.js') }}"></script> --}}
<script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>


<script>
    var myDropzone;
    $('#uploadedFiles').click(function () {

        var url = '{{ route("admin.all-tasks.show-files", ':id') }}';

        var id = {{ $task->id }};
        url = url.replace(':id', id);

        $('#subTaskModelHeading').html('Sub Task');
        $.ajaxModal('#subTaskModal', url);
    });


        Dropzone.autoDiscover = false;
        //Dropzone class
        myDropzone = new Dropzone("#file-upload-dropzone ", {
            url: "{{ route('admin.task-files.store') }}",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            paramName: "file",
         //   maxFilesize: 10,
            maxFiles: 10,
            uploadMultiple: true,
            addRemoveLinks:true,
            parallelUploads:10,
            dictDefaultMessage: "@lang('modules.projects.dropFile')",
            init: function () {
                this.on("success", function (file, response) {
                    if(response.status == 'fail') {
                        $.showToastr(response.message, 'error');
                        return;
                    }
                         var viewName = $('#view').val();
                        if(viewName == 'list') {
                            $('#files-list-panel ul.list-group').html(response.html);
                            this.removeAllFiles();
                        } else {
                            $('#thumbnail').empty();
                            $(response.html).hide().appendTo("#thumbnail").fadeIn(500);
                            this.removeAllFiles();
                        }
                    $('#totalUploadedFiles').html(response.totalFiles);
                    $('#files-list').html(response.html);

                })
            }
        });

        //dropzone for comment



    $('#show-dropzone').click(function () {
        $('#file-dropzone').toggleClass('hide show');
        myDropzone.removeAllFiles();

    });
    $('#show-dropzone-comment').click(function () {
        $('#file-dropzone-comment').toggleClass('hide show');
        myDropzone.removeAllFiles();

    });

    $('body').on('click', '.edit-sub-task', function () {
        var id = $(this).data('sub-task-id');
        var url = '{{ route('admin.sub-task.edit', ':id')}}';
        url = url.replace(':id', id);

        $('#subTaskModelHeading').html('Sub Task');
        $.ajaxModal('#subTaskModal', url);
    })

    $('.add-sub-task').click(function () {
        var id = $(this).data('task-id');
        var url = '{{ route('admin.sub-task.create')}}?task_id='+id;

        $('#subTaskModelHeading').html('Sub Task');
        $.ajaxModal('#subTaskModal', url);
    })

    $('#reminderButton').click(function () {
        swal({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.confirmation.reminderEmployee')",
            dangerMode: true,
            icon: 'warning',
            buttons: {
                cancel: "@lang('messages.confirmNoArchive')",
                confirm: {
                    text: "@lang('messages.sendConfirmation')",
                    value: true,
                    visible: true,
                    className: "danger",
                }
            }
        }).then( function (isConfirm) {
            if (isConfirm) {

                var url = '{{ route('admin.all-tasks.reminder', $task->id)}}';

                $.easyAjax({
                    type: 'GET',
                    url: url,
                    success: function (response) {
                       //
                    }
                });
            }
        });
    })

    $('body').on('click', '.delete-sub-task', function () {
        var id = $(this).data('sub-task-id');
        var task_id = {{ $task->id }};
        swal({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.confirmation.deleteSubtask')!",
            dangerMode: true,
            icon: 'warning',
            buttons: {
                cancel: "@lang('messages.confirmNoArchive')",
                confirm: {
                    text: "@lang('messages.deleteConfirmation')",
                    value: true,
                    visible: true,
                    className: "danger",
                }
            }
        }).then(function (isConfirm) {
            if (isConfirm) {

                var url = "{{ route('admin.sub-task.destroy',':id' ) }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE', task_id:task_id},
                    success: function (response) {
                        var length = (response.data.subTasks).length;
                        if (response.status == "success") {
                            if(length > 0){
                                $('#percentage-count').html(`<span class="pull-right"><span class="donut">${response.data.completedSubtasks}/${response.data.totalSubTasks}</span> <span class="text-muted font-12">${response.data.percentageTaskCompleted}%</span></span>`);
                              $('#subtask-count').text("@lang('modules.tasks.subTask')" +'('+(response.data.totalSubTasks)+')');

                            }else{
                                $("#percentage-count").text("0/0 0%");
                                $('#subtask-count').text("@lang('modules.tasks.subTask')" +'('+('0')+')');

                            }

                            $('#sub-task-list').html(response.view);
                        }
                    }
                });
            }
        });
    });

    $('#view-task-history').click(function () {
        var id = $(this).data('task-id');

        var url = '{{ route('admin.all-tasks.history', ':id')}}';
        url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            type: "GET",
            success: function (response) {
                $('#task-history-section').html(response.view)
            }
        })

    })

    $('.close-task-history').click(function () {
        $(this).hide();
        $('#task-detail-section').show();
        $('#view-task-history').show();
        $('#task-history-section').html('');
    })

    function saveSubTask() {
        $.easyAjax({
            url: '{{route('admin.sub-task.store')}}',
            container: '#createSubTask',
            type: "POST",
            data: $('#createSubTask').serialize(),

            success: function (response) {
                if (response.status == "success") {
                        $('#percentage-count').html(`<span class="pull-right"><span class="donut">${response.data.completedSubtasks}/${response.data.totalSubTasks}</span> <span class="text-muted font-12">${response.data.percentageTaskCompleted}%</span></span>`);

                        $('#subtask-count').text("@lang('modules.tasks.subTask')" +'('+(response.data.totalSubTasks)+')');

                        $('#sub-task-list').html(response.view)

                }
                $('#subTaskModal').modal('hide');

            }
        })
    }

    function updateSubTask(id) {
        var url = '{{ route('admin.sub-task.update', ':id')}}';
            url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            container: '#createSubTask',
            type: "POST",
            data: $('#createSubTask').serialize(),
            success: function (response) {
                $('#sub-task-list').html(response.view)
                $('#subTaskModal').modal('toggle');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            }
        })
    }

    $('.summernote').summernote({
        height: 100,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,                 // set focus to editable area after initializing summernote,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ["view", ["fullscreen", "codeview"]]
        ]
    });

    //    change sub task status
    $('#sub-task-list').on('click', '.task-check', function () {
        if ($(this).is(':checked')) {
            var status = 'complete';
        }else{
            var status = 'incomplete';
        }

        var id = $(this).data('sub-task-id');
        var task_id = {{ $task->id }};

        var url = "{{route('admin.sub-task.changeStatus')}}";
        var token = "{{ csrf_token() }}";

        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': token, subTaskId: id, status: status ,task_id:task_id},
            success: function (response) {
                if (response.status == "success") {
                    $('#percentage-count').html(`<span class="pull-right"><span class="donut">${response.data.completedSubtasks}/${response.data.totalSubTasks}</span> <span class="text-muted font-12">${response.data.percentageTaskCompleted}%</span></span>`);
                    $('#sub-task-list').html(response.view);

                }
            }
        })
    });
    @if($upload)
    Dropzone.autoDiscover = false;
    //Dropzone class
    myDropzone = new Dropzone("#file-upload-dropzone-comment", {
        url: "{{ route('admin.task-comment.comment-file') }}",
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        paramName: "file",
        maxFilesize: 10,
        maxFiles: 10,
        acceptedFiles: "image/*,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/docx,application/pdf,text/plain,application/msword,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        autoProcessQueue: false,
        uploadMultiple: true,
        addRemoveLinks:true,
        parallelUploads:10,
        dictDefaultMessage: "@lang('modules.projects.dropFile')",
        init: function () {
            myDropzone = this;
            this.on("success", function (file, response) {
                if(response.status == 'fail') {
                    $.showToastr(response.message, 'error');
                    return;
                }
                    $('#comment-list').html(response.view);
                     $('#files-list-panel ul.list-group').html(response.html);
                    this.removeAllFiles();
            })
        }
    });

    myDropzone.on('sending', function(file, xhr, formData) {
        console.log(myDropzone.getAddedFiles().length,'sending');
        var ids = $('#taskID').val();
        var comment_id = $('#commentID').val();
        formData.append('task_id', ids);
        formData.append('comment_id',comment_id);
    });

    myDropzone.on('completemultiple', function () {
        var msgs = "@lang('messages.taskCreatedSuccessfully')";
        $.showToastr(msgs, 'success');
        return;
    });
    @endif

    $('#submit-comment').click(function () {
        var comment = $('#task-comment').val();
        var token = '{{ csrf_token() }}';
        $.easyAjax({
        url: '{{ route("admin.task-comment.store") }}',
            type: "POST",
            data: {'_token': token, comment: comment, taskId: '{{ $task->id }}'},
            success: function (response) {
                var dropzone = 0;
                @if($upload)
                    dropzone = myDropzone.getQueuedFiles().length;
                @endif
                if(dropzone > 0){

                    taskID = response.taskID;
                    commentID = response.commentID;
                    $('#taskID').val(response.taskID);
                    $('#commentID').val(response.commentID);
                    myDropzone.processQueue();
                }
                else{
                    var msgs = "@lang('messages.taskCreatedSuccessfully')";
                    $.showToastr(msgs, 'success');
                }
                if (response.status == "success") {
                    $('#comment-count').text("@lang('modules.tasks.comment')" +'('+(response.data.totalComment)+')');
                    $('#comment-list').html(response.view);
                    $('.summernote').summernote("reset");
                    $('.note-editable').html('');
                    $('#task-comment').val('');
                }
            }
        })
    })
    $('#submit-note').click(function () {
        var note = $('#task-note').val();
        var token = '{{ csrf_token() }}';
        $.easyAjax({
            url: '{{ route("admin.task-note.store") }}',
            type: "POST",
            data: {'_token': token, note: note, taskId: '{{ $task->id }}'},
            success: function (response) {
                if (response.status == "success") {
                    $('#notes-count').text("@lang('app.notes')" +'('+(response.data.notesCount)+')');
                    $('#note-list').html(response.view);
                    $('.summernote').summernote("reset");
                    $('.note-editable').html('');
                    $('#task-note').val('');
                }
            }
        })
    })
    function deleteNote (id) {

        var url = '{{ route("admin.task-note.destroy", ':id') }}';
        url = url.replace(':id', id);

        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': '{{ csrf_token() }}', '_method': 'DELETE'},
            success: function (response) {
                if (response.status == "success") {
                    $('#note-list').html(response.view);
                }
            }
        })
    }
    function deleteComment(id) {
        var commentId = id;
        var token = '{{ csrf_token() }}';
        var url = '{{ route("admin.task-comment.destroy", ':id') }}';
        url = url.replace(':id', commentId);

        $.easyAjax({
            url: url,
            type: "POST",
            container: '#comment-list',
            data: {'_token': token, '_method': 'DELETE', commentId: commentId},
            success: function (response) {
                if (response.status == "success") {
                    $('#comment-list').html(response.view);
                    $('#comment-count').text("@lang('modules.tasks.comment')" +'('+(response.data.totalComment)+')');
                    $('.note-editable').html('');
                }
            }
        })
    }
    //    change task status
   function markComplete(status) {

        var id = '{{ $task->id }}';

        if(status == 'completed'){
            var checkUrl = '{{route('admin.tasks.checkTask', ':id')}}';
            checkUrl = checkUrl.replace(':id', id);
            $.easyAjax({
                url: checkUrl,
                type: "GET",
                container: '#task-list-panel',
                data: {},
                success: function (data) {
                    if(data.taskCount > 0){
                        swal({
                            title: "@lang('messages.sweetAlertTitle')",
                            text: "@lang('messages.confirmation.markTaskComplete')",
                            dangerMode: true,
                            icon: 'warning',
                            buttons: {
                                cancel: "@lang('messages.confirmNoArchive')",
                                confirm: {
                                    text: "@lang('messages.completeIt')",
                                    value: true,
                                    visible: true,
                                    className: "danger",
                                }
                            }
                        }).then(function (isConfirm) {
                            if (isConfirm) {
                                updateTask(id,status)
                            }
                        });
                    }
                    else{
                        updateTask(id,status)
                    }

                }
            });
        }
        else{
            updateTask(id,status)
        }


    }

    // Update Task
    function updateTask(id,status){
        var url = "{{route('admin.tasks.changeStatus')}}";
        var token = "{{ csrf_token() }}";
        $.easyAjax({
            url: url,
            type: "POST",
            container: '.r-panel-body',
            data: {'_token': token, taskId: id, status: status, sortBy: 'id'},
            success: function (data) {
                $('#columnStatus').css('color', data.textColor);
                $('#columnStatus').html(data.column);
                if(status == 'completed'){

                    $('#inCompletedButton').removeClass('hidden');
                    $('#completedButton').addClass('hidden');
                    $('#reminderButton').addClass('hidden');
                }
                else{
                    $('#completedButton').removeClass('hidden');
                    $('#inCompletedButton').addClass('hidden');
                    $('#reminderButton').removeClass('hidden');
                }

                if( typeof table !== 'undefined'){
                    window.LaravelDataTables["allTasks-table"].draw();
                }
                else if(typeof loadData !== 'undefined' && $.isFunction(loadData)){
                    loadData();
                }
            }
        })
    }



</script>

<script>
    $('body').on('click', '.file-delete', function () {
        var id = $(this).data('file-id');
        swal({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.confirmation.deleteFile')",
            dangerMode: true,
            icon: 'warning',
            buttons: {
                cancel: "@lang('messages.confirmNoArchive')",
                confirm: {
                    text: "@lang('messages.deleteConfirmation')!",
                    value: true,
                    visible: true,
                    className: "danger",
                }
            }
        }).then(function (isConfirm) {
            if (isConfirm) {

                var url = "{{ route('admin.task-files.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $('#task-file-'+id).remove();
                            $('#totalUploadedFiles').html(response.totalFiles);
                            $('#list ul.list-group').html(response.html);
                        }
                    }
                });
            }
        });
    });

    $('body').on('click', '.task-file-delete', function () {
        var id = $(this).data('file-id');
        swal({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.confirmation.deleteFile')",
            dangerMode: true,
            icon: 'warning',
            buttons: {
                cancel: "@lang('messages.confirmNoArchive')",
                confirm: {
                    text: "@lang('messages.deleteConfirmation')!",
                    value: true,
                    visible: true,
                    className: "danger",
                }
            }
        }).then(function (isConfirm) {
            if (isConfirm) {

                var url = "{{ route('admin.sub-task-files.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $('#sub-task-file-'+id).remove();
                            // $('.sub-task-file').remove();
                            // $('#fileList').after(response.html);
                        }
                    }
                });
            }
        });
    });

    $('body').on('click', '.comment-file-delete', function () {
        var id = $(this).data('file-id');
        swal({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.confirmation.deleteFile')",
            dangerMode: true,
            icon: 'warning',
            buttons: {
                cancel: "@lang('messages.confirmNoArchive')",
                confirm: {
                    text: "@lang('messages.deleteConfirmation')!",
                    value: true,
                    visible: true,
                    className: "danger",
                }
            }
        }).then(function (isConfirm) {
            if (isConfirm) {
                var url = "{{ route('admin.task-comment.comment-file-delete',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $('#task-comment-file-'+id).remove();
                            $('#totalUploadedFiles').html(response.totalFiles);
                            $('#comment-files-list').html(response.html);
                            $('#list ul.list-group').html(response.html);
                        }
                    }
                });
            }
        });
    });

    $('body').on('click', '#pinnedItem', function(){
        var type = $('#pinnedItem').attr('data-pinned');
        var id = {{ $task->id }};
        var pinType = 'task';

        var dataPin = type.trim(type);
        if(dataPin == 'pinned'){
            swal({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.removeFileText')",
                dangerMode: true,
                icon: 'warning',
                buttons: {
                    cancel: "@lang('messages.confirmNoArchive')",
                    confirm: {
                        text: "@lang('messages.unpinIt')",
                        value: true,
                        visible: true,
                        className: "danger",
                    }
                }
            }).then(function (isConfirm) {
                if (isConfirm) {
                    var url = "{{ route('admin.pinned.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE','type':pinType},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                $('.pin-icon').removeClass('pinned');
                                $('.pin-icon').addClass('unpinned');
                                $('#pinnedItem').attr('data-pinned','unpinned');
                                $('#pinnedItem').attr('data-original-title','Pin');
                                $("#pinnedItem").tooltip("hide");
                                window.LaravelDataTables["allTasks-table"].draw();
                            }
                        }
                    })
                }
            });
        }
        else {
            swal({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.pinTask')",
                dangerMode: true,
                icon: 'warning',
                buttons: {
                    cancel: "@lang('messages.confirmNoArchive')",
                    confirm: {
                        text: "@lang('messages.pinIt')",
                        value: true,
                        visible: true,
                        className: "danger",
                    }
                }
            }).then(function (isConfirm) {
                if (isConfirm) {
                    var url = "{{ route('admin.pinned.store') }}?type="+pinType;

                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token,'task_id':id},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                $('.pin-icon').removeClass('unpinned');
                                $('.pin-icon').addClass('pinned');
                                $('#pinnedItem').attr('data-pinned','pinned');
                                $('#pinnedItem').attr('data-original-title','Unpin');
                                $("#pinnedItem").tooltip("hide");
                                window.LaravelDataTables["allTasks-table"].draw();
                            }
                        }
                    });
                }
            });
        }
    });

</script>