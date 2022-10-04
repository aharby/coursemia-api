@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('content')
    <div class="row">
        @if (!$rows->isEmpty())
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                    <tr>
                        <th class="text-center">{{ trans('courses.Name') }}</th>
                        <th class="text-center">{{ trans('courses.Instructor') }}</th>
                        <th class="text-center">{{ trans('courses.Type') }}</th>
                        <th class="text-center">{{ trans('courses.Sessions Count') }}</th>
                        <th class="text-center">{{ trans('courses.created on') }}</th>
                        <th class="text-center">{{ trans('courses.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr class="text-center">
                            <td>{{ $row->name }}</td>
                            <td>{{ "{$row->instructor->first_name} {$row->instructor->last_name}" }}</td>
                            <td>{{ $row->type }}</td>
                            <td>{{ $row->sessions->count() }}</td>
                            <td>{{ $row->created_at }}</td>
                            <td>
                                <div class="row">
                                    <div class="col-md-2 col-sm-2 col-xs-2 " data-step="2"
                                        data-intro="{{ trans('introJs.You Can View Courses!') }}" data-position='right'>
                                        <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                            <a class="btn btn-xs btn-primary"
                                                href="{{ route('admin.courses.get.view', $row->id) }}"
                                                data-toggle="tooltip" data-placement="top"
                                                data-title="{{ trans('course.view') }}">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-2 " data-step="2"
                                        data-intro="{{ trans('introJs.You Can View Sessions!') }}" data-position='right'>
                                        <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                            <a class="btn btn-xs btn-primary"
                                                href="{{ route('admin.vcr-sessions.vcr-sessions.courses.attendance', $row->id) }}"
                                                data-toggle="tooltip" data-placement="top"
                                                data-title="{{ trans('app.course session') }}">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pull-right">
                {{ $rows->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
