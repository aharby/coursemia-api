@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.countries.get.edit',$row->id) }}" class="btn btn-success">{{ trans('app.Edit') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>
            @foreach(config("translatable.locales") as $lang)
            <tr>
                <th width="25%" class="text-center">{{ trans('countries.name').' '.$lang }}</th>
                <td width="75%" class="text-center">{{ $row->translateOrDefault($lang)->name }}</td>
            </tr>
            @endforeach
            <tr>
                <th width="25%" class="text-center">{{ trans('countries.Country Code') }}</th>
                <td width="75%" class="text-center">{{ $row->country_code }}</td>
            </tr>
            @foreach(config("translatable.locales") as $lang)
            <tr>
                <th width="25%" class="text-center">{{ trans('countries.currency') . ' ' .$lang }}</th>
                <td width="75%" class="text-center">{{ $row->translateOrDefault($lang)->currency}}</td>
            </tr>
            @endforeach
            <tr>
                <th width="25%" class="text-center">{{ trans('countries.Is active') }}</th>
                <td width="75%" class="text-center">{!!  $row->is_active ? '<span class="label label-primary">'.trans('app.active') : '<span class="label label-danger">'.trans('app.not active') !!}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
