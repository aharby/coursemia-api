@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.academicYears.get.edit',$row->id) }}" class="btn btn-success">{{ trans('app.Edit') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>
            @foreach(config("translatable.locales") as $lang)
            <tr>
                <th width="20%">{{ trans('academic_years.name').' '.$lang }}</th>
                <td>{{ $row->translateOrDefault($lang)->name }}</td>
            </tr>
            @endforeach
            <tr>
                <th width="20%">{{ trans('academic_years.Country') }}</th>
                <td>{{  $row->country->name }}</td>
            </tr>
            <tr>
                <th width="20%">{{ trans('academic_years.Educational System') }}</th>
                <td>{{  $row->educationalSystem->name }}</td>
            </tr>
            <tr>
                <th width="20%">{{ trans('academic_years.Is active') }}</th>
                <td>{!!  $row->is_active ? '<span class="label label-primary">'.trans('app.active') : '<span class="label label-danger">'.trans('app.not active') !!}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
