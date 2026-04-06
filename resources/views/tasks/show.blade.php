@extends('app')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="row">
        <div class="col-md-12">
            <h3 style="margin-top: 0; margin-bottom: 20px;">
                <i class="fa fa-eye"></i> Task Details
            </h3>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-8">
                            <h3 class="panel-title" style="line-height: 2;">
                                #{{ $task->task_id }} - {{ $task->t_title }}
                            </h3>
                        </div>
                        <div class="col-xs-4 text-right">
                            <a href="{{ route('tasks.index') }}" class="btn btn-default btn-xs">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                            <a href="{{ route('tasks.edit', $task->task_id) }}" class="btn btn-primary btn-xs">
                                <i class="fa fa-pencil"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    {{-- Status Badge Logic --}}
                    @php
                        $statusLabel = 'label-danger';
                        $statusText = 'Incomplete';
                        if($task->status == 1) { $statusLabel = 'label-warning'; $statusText = 'In Progress'; }
                        elseif($task->status == 2) { $statusLabel = 'label-success'; $statusText = 'Completed'; }
                    @endphp

                    <div class="form-horizontal">
                        <div class="form-group" style="margin-bottom: 5px;">
                            <label class="col-sm-3 text-right text-muted">Status:</label>
                            <div class="col-sm-9"><span class="label {{ $statusLabel }}">{{ $statusText }}</span></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 5px;">
                            <label class="col-sm-3 text-right text-muted">Department:</label>
                            <div class="col-sm-9"><strong>{{ $task->department->dept_name ?? 'N/A' }}</strong></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 5px;">
                            <label class="col-sm-3 text-right text-muted">Assigned To:</label>
                            <div class="col-sm-9"><i class="fa fa-user"></i> {{ $task->assignedTo->fullname ?? 'N/A' }}</div>
                        </div>
                        <div class="form-group" style="margin-bottom: 5px;">
                            <label class="col-sm-3 text-right text-muted">Assigned By:</label>
                            <div class="col-sm-9"><i class="fa fa-user-secret"></i> {{ $task->assignedBy->fullname ?? 'N/A' }}</div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="col-sm-3 text-right text-muted">Description:</label>
                            <div class="col-sm-9"><p class="lead" style="font-size: 14px;">{{ $task->t_description ?? 'No description.' }}</p></div>
                        </div>

                        <hr>

                        {{-- Dates (Explicitly Parsed as Asia/Karachi for correct display) --}}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="alert alert-info" style="margin-bottom: 5px;">
                                    <strong>Start Time:</strong><br>
                                    {{ \Carbon\Carbon::parse($task->t_start_time, 'Asia/Karachi')->format('M d, Y - h:i A') }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="alert alert-warning" style="margin-bottom: 5px;">
                                    <strong>Due Date:</strong><br>
                                    {{ \Carbon\Carbon::parse($task->t_end_time, 'Asia/Karachi')->format('M d, Y - h:i A') }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="panel-footer text-right">
                     <small class="text-muted">Task Created by <strong>{{ $task->assignedBy->fullname ?? 'Unknown' }}</strong></small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
