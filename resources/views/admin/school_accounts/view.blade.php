@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.school-accounts.get.edit',$row->id) }}"
           class="btn btn-success">{{ trans('app.Edit') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>

            <tr>
                <th width="25%" class="text-center">@lang('school-accounts.Name')</th>
                <td width="75%" class="text-center">{{ $row->name }}</td>
            </tr>

            @if ($row->manager_email)
                    <tr>
                        <th width="25%" class="text-center">@lang('school-accounts.manager_email')</th>
                        <td width="75%" class="text-center">{{ $row->manager_email }}</td>
                    </tr>
            @endif
           
            <tr>
                <th width="25%" class="text-center">@lang('school-accounts.Country')</th>
                <td width="75%" class="text-center">{{ $row->country->name ?? '' }}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">@lang('school-accounts.Educational Terms')</th>
                <td width="75%" class="text-center">{{ implode(', ',$row->educationalTerms->pluck('title')->toArray()) ?? '' }}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">@lang('school-accounts.academic years')</th>
                <td width="75%" class="text-center">{{ implode(', ',$row->academicYears->pluck('title')->toArray()) ?? '' }}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">@lang('school-accounts.educational Systems')</th>
                <td width="75%" class="text-center">{{ implode(', ',$row->educationalSystems->pluck('name')->toArray()) ?? '' }}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">@lang('school-accounts.grade Classes')</th>
                <td width="75%" class="text-center">{{ implode(', ',$row->gradeClasses->pluck('title')->toArray()) ?? '' }}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">@lang('school-accounts.Created at')</th>
                <td width="75%" class="text-center">{{ $row->created_at ?? '' }}</td>
            </tr>

            <tr>
                <th class="text-center">{{ trans('school-accounts.Is active') }}</th>
                <td class="text-center">{!!  $row->is_active ? '<span class="label label-primary">'.trans('school-accounts.active') : '<span class="label label-danger">'.trans('school-accounts.not active') !!}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
