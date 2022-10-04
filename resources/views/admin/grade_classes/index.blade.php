@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.gradeClasses.get.create') }}" class="btn btn-success">{{ trans('grade_classes.Create') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('grade_classes.title') }}</th>
                    <th class="text-center">{{ trans('grade_classes.Country') }}</th>
                    <th class="text-center">{{ trans('grade_classes.Educational System') }}</th>
                    <th class="text-center">{{ trans('grade_classes.Is active') }}</th>
                    <th class="text-center">{{ trans('grade_classes.created on') }}</th>
                    <th class="text-center">{{ trans('grade_classes.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->title }}</td>
                        <td>{{ $row->country->name ?? '' }}</td>
                        <td>{{ $row->educationalSystem->name ?? '' }}</td>
                        <td>{!!  $row->is_active ? '<span class="label label-primary">'.trans('grade_classes.active') : '<span class="label label-danger">'.trans('grade_classes.not active') !!}</td>
                        <td>{{ $row->created_at }}</td>
                        <td>
                            <div class="row">
                                <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                    <a class="btn btn-xs btn-primary" href="{{  route('admin.gradeClasses.get.view',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('grade_classes.view') }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                    <a class="btn btn-xs btn-info" href="{{  route('admin.gradeClasses.get.edit',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('grade_classes.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                    <a class="btn btn-xs btn-success" href="{{  route('admin.gradeClasses.get.logs',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('app.Logs') }}">
                                        <i class="fa fa-bar-chart"></i>
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                    <form method="POST" class="" action="{{route('admin.gradeClasses.delete' , $row->id)}}">
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
