@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.options.get.edit',$row->id) }}"
           class="btn btn-success">{{ trans('app.Edit') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>
            @foreach(config("translatable.locales") as $lang)
                <tr>
                    <th width="25%" class="text-center">{{ trans('options.title').' '.$lang }}</th>
                    <td width="75%" class="text-center">{{ $row->translateOrDefault($lang)->title }}</td>
                </tr>
            @endforeach
            <tr>
                <th width="25%" class="text-center">{{ trans('options.Type') }}</th>
                <td width="75%" class="text-center">{!!  $row->type ??''  !!}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('options.Is active') }}</th>
                <td width="75%" class="text-center">{!!  $row->is_active ? '<span class="label label-primary">'.trans('options.active') : '<span class="label label-danger">'.trans('options.not active') !!}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
