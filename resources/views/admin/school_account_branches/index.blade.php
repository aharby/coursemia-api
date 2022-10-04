@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <div class="col-md-2 col-sm-2 col-xs-2" data-step="1" data-intro="{{trans('introJs.You Can Create Schoo!')}}"  data-position='right' >
            <a href="{{ route('admin.school-account-branches.get.create') }}" class="btn btn-success">{{ trans('school-account-branches.Create') }}</a>
        </div>
    </div>
@endpush
{{--@endif--}}
@section('content')
    <div class="row">
    @if(!empty($rows))
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('school-account-branch.Name') }}</th>
                    <th class="text-center">{{ trans('school-account-branch.School') }}</th>
{{--                    <th class="text-center">{{ trans('school-account-branch.Manager') }}</th>--}}
                    <th class="text-center">{{ trans('school-account-branch.Supervisor') }}</th>
                    <th class="text-center">{{ trans('school-account-branch.Leader') }}</th>
                    <th class="text-center">{{ trans('school-account-branch.Active') }}</th>
{{--                    <th class="text-center">{{ trans('school-account-branch.created on') }}</th>--}}
                    <th class="text-center">{{ trans('school-account-branch.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->schoolAccount->name }}</td>
{{--                        <td>{{ $row->schoolAccount->manager_email }}</td>--}}
                        <td>{{ $row->supervisor_email }}</td>
                        <td>{{ $row->leader_email }}</td>
                        <td>{!!  $row->is_active ? '<span class="label label-primary">'.trans('users.active') : '<span class="label label-danger">'.trans('users.not active') !!}</td>
{{--                        <td>{{ $row->created_at }}</td>--}}
                        <td>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2 " data-step="2" data-intro="{{trans('introJs.You Can View School Account Branch!')}}"  data-position='right' >
                                    <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                        <a class="btn btn-xs btn-primary"
                                           href="{{  route('admin.school-account-branches.get.view',$row->id) }}"
                                           data-toggle="tooltip" data-placement="top"
                                           data-title="{{ trans('school-account-branch.view') }}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-2 col-xs-2 " data-step="4" data-intro="{{trans('introJs.You Can Edit School Account Branch!')}}"  data-position='right' >
                                    <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                        <a class="btn btn-xs btn-info"
                                           href="{{  route('admin.school-account-branches.get.edit',$row->id) }}"
                                           data-toggle="tooltip" data-placement="top"
                                           data-title="{{ trans('school-account-branch.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-2 col-sm-2 col-xs-2 " data-position='right' >
                                    <form method="POST" class="d-inline"
                                          action="{{route('admin.school-account-branches.delete' , $row->id)}}">
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
