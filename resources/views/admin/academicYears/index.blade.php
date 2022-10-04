@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.academicYears.get.create') }}" class="btn btn-success">{{ trans('academic_years.Create') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th width="20%">{{ trans('academic_years.name') }}</th>
                    <th width="20%">{{ trans('academic_years.Is active') }}</th>
                    <th width="20%">{{ trans('academic_years.created on') }}</th>
                    <th>{{ trans('academic_years.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr>
                        <td>{{ $row->name }}</td>
                        <td>{!!  $row->is_active ? '<span class="label label-primary">'.trans('academic_years.active') : '<span class="label label-danger">'.trans('academic_years.not active') !!}</td>
                        <td>{{ $row->created_at }}</td>
                        <td>
                            <div class="row">
                                <div class="col-md-4 col-sm-4 col-xs-4 form-group">
                                    <a class="btn btn-xs btn-primary" href="{{  route('admin.academicYears.get.view',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('subject_fields.view') }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-4 form-group">
                                    <a class="btn btn-xs btn-info" href="{{  route('admin.academicYears.get.edit',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('subject_fields.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-4 form-group">
                                    <form method="POST" class="" action="{{route('admin.academicYears.delete' , $row->id)}}">
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
