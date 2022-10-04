@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.psychological_recomendations.get.edit',$row->id) }}"
           class="btn btn-success">{{ trans('app.Edit') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>

            @foreach(config("translatable.locales") as $lang)
                <tr>
                    <td width="25%" class="text-center">{{trans('psychological_recomendations.Result').' '.$lang}}</td>
                    <td width="75%" class="text-center">{!! $row->translateOrDefault($lang)->result !!}</td>
                </tr>
            @endforeach

            @foreach(config("translatable.locales") as $lang)
                <tr>
                    <td width="25%" class="text-center">{{trans('psychological_recomendations.Recomendation').' '.$lang}}</td>
                    <td width="75%" class="text-center">{!! $row->translateOrDefault($lang)->recomendation !!}</td>
                </tr>
            @endforeach

            <tr>
                <th width="25%" class="text-center">@lang('psychological_recomendations.From')</th>
                <td width="75%" class="text-center">{{ $row->from }}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">@lang('psychological_recomendations.To')</th>
                <td width="75%" class="text-center">{{ $row->to }}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('psychological_recomendations.Is active') }}</th>
                <td class="text-center">{!!  $row->is_active ? '<span class="label label-primary">'.trans('app.active') : '<span class="label label-danger">'.trans('app.not active') !!}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
