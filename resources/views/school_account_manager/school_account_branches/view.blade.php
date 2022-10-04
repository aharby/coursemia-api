@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@section('buttons')
    <div class="row">
        <a href="{{ route('school-account-manager.school-account-branches.get.edit',$row->id) }}"
           class="btn btn-success">{{ trans('app.Edit') }}</a>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>

            <tr>
                <th width="25%" class="text-center">@lang('school-account-branch.Name')</th>
                <td width="75%" class="text-center">{{ $row->name }}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">@lang('school-account-branch.School Name')</th>
                <td width="75%" class="text-center">{{ $row->schoolAccount->name ?? ''}}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">@lang('school-account-branch.School Account Manager')</th>
                <td width="75%" class="text-center">{{ $row->schoolAccount->manager->name ?? '' }}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">@lang('school-account-branch.Branch Leader')</th>
                <td width="75%" class="text-center">{{ $row->leader->name ?? ''}}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">@lang('school-account-branch.Branch Supervisor')</th>
                <td width="75%" class="text-center">{{ $row->supervisor->name ?? ''}}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">@lang('school-account-branch.Is Active')</th>
                <td width="75%" class="text-center">{{ $row->is_active? trans('app.active'): trans('not active') }}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">@lang('school-account-branch.Created at')</th>
                <td width="75%" class="text-center">{{ $row->created_at ?? '' }}</td>
            </tr>

            </tbody>
        </table>
                    </div></div></div></div>
    </div>
@endsection
