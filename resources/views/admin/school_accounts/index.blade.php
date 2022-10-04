@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <div class="col-md-2 col-sm-2 col-xs-2" data-step="1" data-intro="{{trans('introJs.You Can Create New School Account!')}}"  data-position='right' >
            <a href="{{ route('admin.school-accounts.get.create') }}" class="btn btn-success">{{ trans('school-account.Create') }}</a>
        </div>
    </div>
@endpush
@section('content')
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('school-account.Name') }}</th>
                    <th class="text-center">{{ trans('school-account.Manager Id') }}</th>
                    <th class="text-center">{{ trans('school-account.Country') }}</th>
                    <th class="text-center">{{ trans('school-account.Active') }}</th>
                    <th class="text-center">{{ trans('school-account.created on') }}</th>
                    <th class="text-center">{{ trans('school-account.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->manager->username ?? ''}}</td>
                        <td>{{ $row->country->name ?? ''  }}</td>
                        <td>{!!  $row->is_active ? '<span class="label label-primary">'.trans('users.active') : '<span class="label label-danger">'.trans('users.not active') !!}</td>
                        <td>{{ $row->created_at }}</td>
                        <td>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2 " data-step="2" data-intro="{{trans('introJs.You Can View School Accounts!')}}"  data-position='right' >
                                    <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                        <a class="btn btn-xs btn-primary"
                                           href="{{  route('admin.school-accounts.get.view',$row->id) }}"
                                           data-toggle="tooltip" data-placement="top"
                                           data-title="{{ trans('school-accounts.view') }}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-2 col-sm-2 col-xs-2 " data-step="4" data-intro="{{trans('introJs.You Can Edit School Account!')}}"  data-position='right' >
                                    <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                        <a class="btn btn-xs btn-info"
                                           href="{{  route('admin.school-accounts.get.edit',$row->id) }}"
                                           data-toggle="tooltip" data-placement="top"
                                           data-title="{{ trans('school-accounts.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
{{--                                <div class="col-md-2 col-sm-2 col-xs-2 " data-step="5" data-intro="{{trans('introJs.You Can View Courses Log!')}}"  data-position='right' >--}}
{{--                                    <div class="col-md-3 col-sm-3 col-xs-3 form-group">--}}
{{--                                        <a class="btn btn-xs btn-success" href="{{  route('admin.courses.get.logs',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('app.Logs') }}">--}}
{{--                                            <i class="fa fa-bar-chart"></i>--}}
{{--                                        </a>--}}
{{--                                    </div>--}}
{{--                                </div>--}}

                                <div class="col-md-2 col-sm-2 col-xs-2 " data-position='right' >
                                    <form method="POST" class="d-inline"
                                          action="{{route('admin.school-accounts.delete' , $row->id)}}">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button type="submit" class="btn btn-xs btn-danger" value="Delete Station"
                                                data-confirm="{{trans('app.Are you sure you want to delete this item')}}?">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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
