@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.schools.get.create') }}" class="btn btn-success">{{ trans('schools.Create') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('schools.name') }}</th>
                    <th class="text-center">{{ trans('schools.mobile') }}</th>
                    <th class="text-center">{{ trans('schools.email') }}</th>
                    <th class="text-center">{{ trans('schools.Is active') }}</th>
                    <th class="text-center">{{ trans('schools.created on') }}</th>
                    <th class="text-center">{{ trans('schools.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->mobile }}</td>
                        <td>{{ $row->email }}</td>
                        <td>{!!  $row->is_active ? '<span class="label label-primary">'.trans('schools.active') : '<span class="label label-danger">'.trans('schools.not active') !!}</td>
                        <td>{{ $row->created_at }}</td>
                        <td>
                            <div class="row">
                                <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                    <a class="btn btn-xs btn-primary"
                                       href="{{  route('admin.schools.get.view',$row->id) }}" data-toggle="tooltip"
                                       data-placement="top" data-title="{{ trans('schools.view') }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                    <a class="btn btn-xs btn-info"
                                       href="{{  route('admin.schools.get.edit',$row->id) }}" data-toggle="tooltip"
                                       data-placement="top" data-title="{{ trans('schools.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                    <a class="btn btn-xs btn-success" href="{{  route('admin.schools.get.logs',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('app.Logs') }}">
                                        <i class="fa fa-bar-chart"></i>
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                    <form method="POST" class="" action="{{route('admin.schools.delete' , $row->id)}}">
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
