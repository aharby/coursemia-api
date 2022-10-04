@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
@if (now()->toDateString() > $course->end_date)
<div class="row">
    <div class="col-md-2 col-sm-2 col-xs-2" data-step="1" data-intro="{{trans('introJs.You Can Create session!')}}"  data-position='right'>
        <a href="{{ route('admin.courses.get.create.session', $course->id) }}" class="btn btn-default"  style="pointer-events: none;">{{ trans('course_sessions.Create') }}</a>
    </div>
    <br><br>
        <p>{{ trans('course_sessions.can not add session') }}</p>
    
   </div>
@else
<div class="row">
    <div class="col-md-2 col-sm-2 col-xs-2" data-step="1" data-intro="{{trans('introJs.You Can Create session!')}}"  data-position='right'>
        <a href="{{ route('admin.courses.get.create.session', $course->id) }}" class="btn btn-success" >{{ trans('course_sessions.Create') }}</a>
    </div>
</div>
@endif
    
@endpush
@section('content')
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-bordered ">
                <thead>
                <tr>
                    <th width="20%">{{ trans('course_sessions.content') }}</th>
                    <th>{{ trans('course_sessions.Date') }}</th>
                    <th width="20%">{{ trans('course_sessions.Status') }}</th>
                    <th>{{ trans('course_sessions.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr>
                        <td>{{ str_limit($row->content) }}</td>
                        <td>{{ $row->date }}</td>
                        <td>
                            {{ $row->status }}
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-md-4 col-sm-4 col-xs-4 form-group">
                                    <a class="btn btn-xs btn-primary"
                                       href="{{  route('admin.courseSessions.get.view',$row->id) }}"
                                       data-toggle="tooltip" data-placement="top" data-title="{{ trans('app.view') }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>
                                @if($row->status == \App\OurEdu\Courses\Enums\CourseSessionEnums::ACTIVE)

                                    <div class="col-md-4 col-sm-4 col-xs-4 form-group">
                                        <a class="btn btn-xs btn-info"
                                           href="{{  route('admin.courseSessions.get.edit',$row->id) }}"
                                           data-toggle="tooltip" data-placement="top"
                                           data-title="{{ trans('app.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                @endif
                                @if( $row->status == \App\OurEdu\Courses\Enums\CourseSessionEnums::ACTIVE)
                                    <div class="col-md-4 col-sm-4 col-xs-4 form-group">
                                        <form method="get" class="" action="{{route('admin.courseSessions.cancel' , $row->id)}}">
                                            <button type="submit" class="btn btn-xs btn-danger" value="{{trans('app.Cancel')}}"
                                                    data-confirm="{{trans('app.Are you sure you want to cancel this item')}}?">
                                                <i class="fa fa-clock-o"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif

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
