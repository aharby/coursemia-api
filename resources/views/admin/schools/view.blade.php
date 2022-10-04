@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.schools.get.edit',$row->id) }}"
           class="btn btn-success">{{ trans('app.Edit') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>
            @foreach(config("translatable.locales") as $lang)
                <tr>
                    <th width="25%" class="text-center">{{ trans('schools.name').' '.$lang }}</th>
                    <td width="75%" class="text-center">{{ $row->translateOrDefault($lang)->name }}</td>
                </tr>
            @endforeach
            <tr>
                <th width="25%" class="text-center">{{ trans('app.Country') }}</th>
                <td width="75%" class="text-center">{!!  $row->country->name ??''  !!}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('app.Educational System') }}</th>
                <td width="75%" class="text-center">{!!  $row->educationalSystem->name ??''  !!}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('app.Address') }}</th>
                <td width="75%" class="text-center">{!!  $row->address ??''  !!}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('app.Email') }}</th>
                <td width="75%" class="text-center">{!!  $row->email ??''  !!}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('app.Mobile') }}</th>
                <td width="75%" class="text-center">{!!  $row->mobile ??''  !!}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('schools.Is active') }}</th>
                <td width="75%"
                    class="text-center">{!!  $row->is_active ? '<span class="label label-primary">'.trans('app.active') : '<span class="label label-danger">'.trans('app.not active') !!}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
