@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.school-account-branches.get.edit',$row->id) }}"
           class="btn btn-success">{{ trans('app.Edit') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>

            <tr>
                <th width="25%" class="text-center">@lang('school-account-branch.Name')</th>
                <td width="75%" class="text-center">{{ $row->name ?? ''}}</td>
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
                <td width="75%" class="text-center">{{ $row->is_active? 'true':'false' }}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">@lang('school-account-branch.Created at')</th>
                <td width="75%" class="text-center">{{ $row->created_at ?? '' }}</td>
            </tr>

            </tbody>
        </table>
    </div>
@endsection
