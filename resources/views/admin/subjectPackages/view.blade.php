@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.subjectPackages.get.edit',$row->id) }}"
           class="btn btn-success">{{ trans('app.Edit') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>
            <tr>
                <th width="25%" class="text-center">{{ trans('subject_packages.Name') }}</th>
                <td width="75%" class="text-center">{{ $row->name }}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('subject_packages.Price') }}</th>
                <td class="text-center">{!!  $row->price !!} {{ trans('subject_packages.riyal') }}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('subject_packages.Country') }}</th>
                <td class="text-center">{!!  $row->country->name ??''  !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('subject_packages.Educational System') }}</th>
                <td class="text-center">{!!  $row->educationalSystem->name ??''  !!}</td>
            </tr>

            <tr>
                <th class="text-center">{{ trans('subject_packages. Academic years') }}</th>
                <td class="text-center">{!!  $row->academicalYears->title ??''  !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('subject_packages.Grade Class') }}</th>
                <td class="text-center">{!!  $row->gradeClass->title ??''  !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('subject_packages.Picture') }}</th>
                <td class="text-center">{!! viewImage($row->picture, 'large') !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('subject_packages.Subjects') }}</th>
                <td class="text-center">
                    @foreach($row->subjects as $subject)
                        {{$subject->name}} <br>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('subject_packages.Is active') }}</th>
                <td class="text-center">{!!  $row->is_active ? '<span class="label label-primary">'.trans('subject_packages.active') : '<span class="label label-danger">'.trans('subject_packages.not active') !!}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
