@extends('layouts.school_manager_layout')
@section('title', @$page_title)
@section('buttons')
            <a href="{{ route('school-account-manager.roles.get.create')}}" class="btn btn-success">{{ trans('app.Create') }}</a>
@endsection
@section('content')
    <div class="section-wrapper">
            @if (!$rows->isEmpty())
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap">
                                <thead>
                                <tr>
                            <th class="wd-15p">{{trans('roles.Title')}} </th>
                            <th class="wd-15p">{{trans('roles.permissions')}} </th>
                            <th class="wd-15p">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                <td class="center">{{$row->title}}</td>
                                <td class="center">{{str_limit(@implode(', ',(@$row->permissions)?:[]),50)}}</td>
                                <td class="center">
                                            <a class="btn btn-success btn-xs" href="{{ route('school-account-manager.roles.get.edit' , [$row->id])}}" title="{{trans('roles.Edit')}}">
                                                <i class="fa fa-edit">{{trans('app.edit')}}</i>
                                            </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                {{trans("roles.There is no results")}}
            @endif
{{--        @endif--}}
    </div>
@endsection
