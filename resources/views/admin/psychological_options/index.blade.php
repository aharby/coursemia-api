@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.psychological_options.get.create', $testId) }}" class="btn btn-success">{{ trans('psychological_options.Create') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('psychological_options.Name') }}</th>
                    <th class="text-center">{{ trans('psychological_options.Points') }}</th>
                    <th class="text-center">{{ trans('psychological_options.Is active') }}</th>
                    <th class="text-center">{{ trans('psychological_options.created on') }}</th>
                    <th class="text-center">{{ trans('psychological_options.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->points }}</td>
                        <td>{!!  $row->is_active ? '<span class="label label-primary">'.trans('app.active') : '<span class="label label-danger">'.trans('app.not active') !!}</td>
                        <td>{{ $row->created_at }}</td>
                        <td>
                            <div class="row">
                                <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                    <a class="btn btn-xs btn-primary"
                                       href="{{ route('admin.psychological_options.get.view',$row->id) }}"
                                       data-toggle="tooltip" data-placement="top"
                                       data-title="{{ trans('app.view') }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                    <a class="btn btn-xs btn-info"
                                       href="{{ route('admin.psychological_options.get.edit',$row->id) }}"
                                       data-toggle="tooltip" data-placement="top"
                                       data-title="{{ trans('app.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                                <form method="POST" class="d-inline"
                                      action="{{ route('admin.psychological_options.delete' , $row->id) }}">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button type="submit" class="btn btn-xs btn-danger" value="Delete Station"
                                            data-confirm="{{trans('app.Are you sure you want to delete this item')}}?">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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
