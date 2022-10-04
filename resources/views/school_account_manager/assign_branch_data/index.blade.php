@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@section('content')
    <div class="row">
        @if(!empty($rows))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('school-account-branches.branch name') }}</th>
                    <th class="text-center">{{ trans('school-account-branches.educational_system') }}</th>
                    <th class="text-center">{{ trans('school-account-branches.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    @if(count($row->educationalSystems) > 0 )
                        @foreach($row->educationalSystems as $educationalSys)
                            <tr class="text-center">
                                <td>{{ $row->name }}</td>
                                <td>{{ $educationalSys->name }}</td>
                                <td>
                                    <div class="row">

                                        <div class="col-md-2 col-sm-2 col-xs-2 " data-step="4"
                                             data-intro="{{trans('introJs.You Can Edit Courses!')}}"
                                             data-position='right'>
                                            <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                                <a class="btn btn-xs btn-info"
                                                   href="{{  route('school-account-manager.branch-grade-classes.get.edit',[$row->id,$educationalSys->id]) }}"
                                                   data-toggle="tooltip" data-placement="top"
                                                   data-title="{{ trans('school-account-branches.edit') }}">
                                                    {{ trans('app.Edit') }}
                                                </a>
                                            </div>
                                        </div>


                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
{{--                        <h5>no educational systems assigned for that branch</h5>--}}
                    @endif
                @endforeach
                </tbody>
            </table>
                        </div></div></div></div>
            <div class="pull-right">
                {{ $rows->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
