@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.subjects.get.edit',$row->id) }}"
           class="btn btn-success">{{ trans('app.Edit') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>
            <tr>
                <th width="25%" class="text-center">@lang('subjects.Name')</th>
                <td width="75%" class="text-center">{{ $row->name }}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('subjects.Country') }}</th>
                <td class="text-center">{!!  $row->country->name ?? ''  !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('subjects.Educational System') }}</th>
                <td class="text-center">{!!  $row->educationalSystem->name ??''  !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('subjects.Educational Term') }}</th>
                <td class="text-center">{!!  $row->educationalTerm->title ??''  !!}</td>
            </tr>
            <tr>
                <th class="text-center">@lang('subjects. Academic years')</th>
                <td class="text-center">{!!  $row->academicalYears->title ??''  !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('subjects.SME') }}</th>
                <td class="text-center">{!!  $row->sme->first_name ??''!!} {!!  $row->sme->last_name??'' !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('subjects.Grade Class') }}</th>
                <td class="text-center">{!!  $row->gradeClass->title ??''  !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('subjects.Start Date') }}</th>
                <td class="text-center">{!!  $row->start_date !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('subjects.End Date') }}</th>
                <td class="text-center">{!!  $row->end_date !!}</td>
            </tr>

            <tr>
                <th class="text-center">{{ trans('subjects.Content Authors') }}</th>
                <td class="text-center">
                    @foreach($row->contentAuthors as $contentAuthor)
                        {{$contentAuthor->first_name}} {{$contentAuthor->last_name}} <br>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('subjects.Instructors') }}</th>
                <td class="text-center">
                    @foreach($row->instructors as $instructors)
                        {{$instructors->first_name}} {{$instructors->last_name}} <br>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('subjects.image') }}</th>
                <td class="text-center">{!! viewImage($row->image, 'large') !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('subjects.Is active') }}</th>
                <td class="text-center">{!!  $row->is_active ? '<span class="label label-primary">'.trans('subjects.active') : '<span class="label label-danger">'.trans('subjects.not active') !!}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
