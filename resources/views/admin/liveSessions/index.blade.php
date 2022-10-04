@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <div class="col-md-2 col-sm-2 col-xs-2" data-step="1" data-intro="{{trans('introJs.You Can Create Live sessions!')}}"  data-position='right' >
            <a href="{{ route('admin.liveSessions.get.create') }}" class="btn btn-success">{{ trans('live_sessions.Create') }}</a>
        </div>
    </div>
@endpush
@section('content')
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('live_sessions.Name') }}</th>
                    <th class="text-center">{{ trans('live_sessions.Is active') }}</th>
                    <th class="text-center">{{ trans('live_sessions.created on') }}</th>
                    <th class="text-center">{{ trans('live_sessions.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->name }}</td>
                        <td>{!!  $row->is_active ? '<span class="label label-primary">'.trans('live_sessions.active') : '<span class="label label-danger">'.trans('course.not active') !!}</td>
                        <td>{{ $row->created_at }}</td>
                        <td>
                            <div class="row">
                                <div class="col-md-2 col-md-2 col-md-2" data-step="2" data-intro="{{trans('introJs.You Can View Live sessions!')}}"  data-position='right' >
                                    <div class="form-group">
                                        <a class="btn btn-xs btn-primary" href="{{  route('admin.liveSessions.get.view',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('course.view') }}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-2 col-md-2 col-md-2" data-step="3" data-intro="{{trans('introJs.You Can Edit Live sessions!')}}"  data-position='right' >
                                    <div class="form-group">
                                        <a class="btn btn-xs btn-info" href="{{  route('admin.liveSessions.get.edit',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('course.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-2 col-md-2 col-md-2" data-step="4" data-intro="{{trans('introJs.You Can View Live sessions Log!')}}"  data-position='right' >
                                    <div class="form-group">
                                        <a class="btn btn-xs btn-success" href="{{  route('admin.liveSessions.get.sessions.logs',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('app.Logs') }}">
                                            <i class="fa fa-bar-chart"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-2 col-md-2 col-md-2" data-step="5" data-intro="{{trans('introJs.You Can Delete Live sessions!')}}"  data-position='right' >
                                    <div class="form-group">
                                        <form method="POST" class="" action="{{route('admin.liveSessions.delete' , $row->id)}}">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-xs btn-danger" value="Delete Station"
                                                    data-confirm="{{trans('app.Are you sure you want to delete this item')}}?">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @if($row->session && $row->session->status == \App\OurEdu\Courses\Enums\CourseSessionEnums::ACTIVE)
                                    <div class="col-md-2 col-md-2 col-md-2" data-step="6" data-intro="{{trans('introJs.You Can Cancel Live sessions!')}}"  data-position='right' >
                                        <div class="form-group">
                                            <form method="get" class="" action="{{route('admin.liveSessions.cancel' , $row->id)}}">
                                            <button type="submit" class="btn btn-xs btn-danger" value="{{trans('app.Cancel')}}"
                                                    data-confirm="{{trans('app.Are you sure you want to cancel this item')}}?">
                                                <i class="fa fa-clock-o"></i>
                                            </button>
                                        </form>
                                    </div>
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
