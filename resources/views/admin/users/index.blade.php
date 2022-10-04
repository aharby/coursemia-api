@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.users.get.create') }}" class="btn btn-success">{{ trans('users.Create') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('users.First name') }}</th>
                    <th class="text-center">{{ trans('users.Last name') }}</th>
                    <th class="text-center">{{ trans('users.Email') }}</th>
                    <th class="text-center">{{ trans('users.Is Active') }}</th>
                    <th class="text-center">{{ trans('users.Type') }}</th>
                    <th class="text-center">{{ trans('users.Created on') }}</th>
                    <th class="text-center">{{ trans('users.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->first_name }}</td>
                        <td>{{ $row->last_name }}</td>
                        <td>{{ $row->email }}</td>
                        <td>{!!  $row->is_active ? '<span class="label label-primary">'.trans('users.active') : '<span class="label label-danger">'.trans('users.not active') !!}</td>
                        <td>{{ $row->type }}</td>
                        <td>{{ $row->created_at }}</td>
                        <td>
                            <div class="row">
                                <div class="col-md-2 form-group">
                                    <a class="btn btn-xs btn-primary" href="{{  route('admin.users.get.view',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('users.view') }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>
                                <div class="col-md-2 form-group">
                                    <a class="btn btn-xs btn-info" href="{{  route('admin.users.get.edit',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('users.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>

                                @if($row->type == App\OurEdu\Users\UserEnums::STUDENT_TEACHER_TYPE)

                                    <div class="col-md-2 form-group">
                                        <a class="btn btn-xs btn-success" href="{{  route('admin.users.get.students',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('users.students') }}">
                                            <i class="fa fa-user"></i>
                                        </a>
                                    </div>

                                @endif

                                <div class="col-md-2 form-group">
                                <form method="POST" class="" action="{{route('admin.users.delete' , $row->id)}}">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button type="submit" class="btn btn-xs btn-danger" value="Delete Station"
                                            data-confirm="{{trans('app.Are you sure you want to delete this item')}}?">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                                </div>
                                <div class="col-md-2 form-group">
                                    @if((auth()->user()->type == \App\OurEdu\Users\UserEnums::SUPER_ADMIN_TYPE
                               && $row->type != \App\OurEdu\Users\UserEnums::SUPER_ADMIN_TYPE )
                               || (auth()->user()->type == \App\OurEdu\Users\UserEnums::ADMIN_TYPE
                               && $row->type != \App\OurEdu\Users\UserEnums::SUPER_ADMIN_TYPE
                               &&  $row->type != \App\OurEdu\Users\UserEnums::ADMIN_TYPE))
                                        <form method="POST" action="{{route('admin.users.suspend' , $row->id)}}">
                                            {{ csrf_field() }}
                                            <button type="submit" class="btn btn-xs btn-{{$row->suspended_at ? 'primary' : 'warning'}}" value="Suspend User"
                                                    data-confirm="{{trans('app.Are you sure you want to ' . $row->suspended_at ? 'remove suspend from' : 'suspend'  .'this user')}}?">
                                                <i class="fa fa-{{$row->suspended_at ? 'play' : 'pause'}}"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <div class="col-md-2 form-group">
                                    <a class="btn btn-xs btn-success" href="{{  route('admin.users.get.logs',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('app.Logs') }}">
                                        <i class="fa fa-bar-chart"></i>
                                    </a>
                                </div>

                                @if($row->type == App\OurEdu\Users\UserEnums::STUDENT_TYPE)
                                    <div class="col-md-2 form-group">
                                        <a class="btn btn-xs btn-primary" href="{{  route('admin.users.index.student.student-teacher',$row->id) }}"
                                           data-toggle="tooltip" data-placement="top" data-title="{{ trans('users.student teachers') }}">
                                            <i class="fa fa-users"></i>
                                        </a>
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
