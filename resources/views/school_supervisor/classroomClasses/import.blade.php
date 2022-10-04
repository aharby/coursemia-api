
@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush

@section('title',@$page_title)

@section('content')

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-6 col-sm-6 col-xs-6 p-2" data-step="2" data-intro="Export Students"  data-position='right' >
                        <a href="{{asset('classes-import.xlsx')}}" class="btn btn-primary">{{ trans('app.Excel file') }}</a>
                    </div>
                    <label class="control-label col-md-6 col-sm-6 col-xs-12 alert-info" for="name">
                        {{ trans('app.Please, use the attached excel template') }}
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['url' => route("school-branch-supervisor.classrooms.classroomClasses.post.import", $classroom),'method' => 'post','class'=>'form-vertical form-label-left' ,  "enctype"=>"multipart/form-data"]) }}

                    <div class="row mg-t-20 form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">
                            {{ trans('classroomClass.import') }}
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">

                            <input type="file" class="form-control" name="excel-data" id="file">
                            <p class="alert-info">{{ trans('app.Please use the attached file') }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-layout-footer">
                            <button type="submit" class="btn btn-success"> {{ trans('app.Save') }}</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    @if(count($importedJobs))
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap">
                                <thead>
                                <tr>
                                    <th class="text-center">{{ trans('ID') }}</th>
                                    <th class="text-center">{{ trans('File') }}</th>
                                    <th class="text-center">{{ trans('Status') }}</th>
                                    <th class="text-center">{{ trans('Errors') }}</th>
                                    <th class="text-center">{{ trans('created_at') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($importedJobs as $job)

                                    <tr class="text-center">
                                        <td>{{ $job->id }} </td>
                                        <td><a target="_blank" href="{{ route('school-branch-supervisor.classrooms.import.download', $job) }}">File</a> </td>
                                        <td>{{ $importedJobsStatus::getLabel($job->status) }}</td>
                                        <td> <a @if($job->has_errors > 0) href="{{ route('school-branch-supervisor.classrooms.import.errors', $job) }}"@endif > {{ $job->has_errors }} </a> </td>
                                        <td>{{ $job->created_at }} </td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                {{ $importedJobs->links() }}
            </div>
        </div>
    @endif

@endsection

