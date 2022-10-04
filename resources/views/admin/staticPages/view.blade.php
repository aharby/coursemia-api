@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.staticPages.get.edit',$row->id) }}"
           class="btn btn-success">{{ trans('app.Edit') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>

            <tr>
                <th class="text-center">{{ trans('staticPage.slug') }}</th>
                <td class="text-center">{{ $row->slug  }}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('staticPage.url') }}</th>
                <td class="text-center">{{$row->url}}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('staticPage.background image') }}</th>
                <td class="text-center">{!! $row->bg_image ? viewImage($row->bg_image, 'large') : trans('app.No image set')!!}</td>
            </tr>

            @foreach(config("translatable.locales") as $lang)
                <tr>
                    <td width="25%" class="text-center">{{trans('staticPage.Title').' '.$lang}}</td>
                    <td width="75%" class="text-center">{{$row->translateOrDefault($lang)->title}}</td>
                </tr>
            @endforeach

            @foreach(config("translatable.locales") as $lang)
                <tr>
                    <td width="25%" class="text-center">{{trans('staticPage.Body').' '.$lang}}</td>
                    <td width="75%" class="text-center">{!! $row->translateOrDefault($lang)->body !!}</td>
                </tr>
            @endforeach

            <tr>
                <th class="text-center">{{ trans('staticPage.Is active') }}</th>
                <td class="text-center">{!!  $row->is_active ? '<span class="label label-primary">'.trans('app.active') : '<span class="label label-danger">'.trans('app.not active') !!}</td>
            </tr>

            </tbody>
        </table>
    </div>
@endsection
