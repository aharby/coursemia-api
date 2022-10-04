@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@section('content')
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('staticPage.Title') }}</th>
                    <th class="text-center">{{ trans('staticPage.Slug') }}</th>
                    <th class="text-center">{{ trans('staticPage.Is active') }}</th>
                    <th class="text-center">{{ trans('staticPage.created on') }}</th>
                    <th class="text-center">{{ trans('staticPage.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->title }}</td>
                        <td>{{ $row->slug }}</td>
                        <td>{!!  $row->is_active ? '<span class="label label-primary">'.trans('staticPage.active') : '<span class="label label-danger">'.trans('staticPage.not active') !!}</td>
                        <td>{{ $row->created_at }}</td>
                        <td>
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-6 form-group">
                                    <a class="btn btn-xs btn-primary" href="{{  route('admin.staticPages.get.view',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('staticPage.view') }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6 form-group">
                                    <a class="btn btn-xs btn-info" href="{{  route('admin.staticPages.get.edit',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('staticPage.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
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
