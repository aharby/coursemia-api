@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.users.get.edit',$row->id) }}" class="btn btn-success">{{ trans('app.Edit') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>
                <tr>
                    <th width="25%" class="text-center">{{ trans('users.First name') }}</th>
                    <td width="75%" class="text-center">{{ $row->first_name }}</td>
                </tr>
                <tr>
                    <th class="text-center">{{ trans('users.Last name') }}</th>
                    <td class="text-center">{{ $row->last_name }}</td>
                </tr>
                <tr>
                    <th class="text-center">{{ trans('users.Email') }}</th>
                    <td class="text-center">{{ $row->email }}</td>
                </tr>
                <tr>
                    <th class="text-center">{{ trans('users.Mobile') }}</th>
                    <td class="text-center">{{ $row->mobile }}</td>
                </tr>
                <tr>
                    <th class="text-center">{{ trans('users.Type') }}</th>
                    <td class="text-center">{{ $row->type }}</td>
                </tr>
                @if($row->type == \App\OurEdu\Users\UserEnums::CONTENT_AUTHOR_TYPE && isset($relation))
                    <tr>
                        <th class="text-center">{{ trans('users.hire date') }}</th>
                        <td class="text-center">{{ $relation->hire_date }}</td>
                    </tr>
                @endif
                @if($row->type == \App\OurEdu\Users\UserEnums::INSTRUCTOR_TYPE && isset($relation))
                    <tr>
                        <th class="text-center">{{ trans('users.about_instructor') }}</th>
                        <td class="text-center">{!!   $relation->about_instructor !!}</td>
                    </tr>
                    <tr>
                        <th class="text-center">{{ trans('users.hire date') }}</th>
                        <td class="text-center">{{ $relation->hire_date ?? ''}}</td>
                    </tr>
                    <tr>
                        <th class="text-center">{{ trans('users.School') }}</th>
                        <td class="text-center">{{ $relation->school->name ?? '' }}</td>
                    </tr>
                @endif
                @if($row->type == \App\OurEdu\Users\UserEnums::STUDENT_TYPE && isset($relation))
                    <tr>
                        <th class="text-center">{{ trans('app.Country') }}</th>
                        <td class="text-center">{!!   $row->country->name ?? '' !!}</td>
                    </tr>
                    <tr>
                        <th class="text-center">{{ trans('subjects.Educational System') }}</th>
                        <td class="text-center">{!!  $relation->educationalSystem->name ??'' !!}</td>
                    </tr>
                    <tr>
                        <th class="text-center">{{ trans('subjects.Grade Class') }}</th>
                        <td class="text-center">{!!  $relation->gradeClass->title ??''  !!}</td>
                    </tr>
                    <tr>
                        <th class="text-center">{{ trans('subjects.Academic year') }}</th>
                        <td class="text-center">{!!  $relation->academicalYear->title ?? null !!}</td>
                    </tr>
                    <tr>
                        <th class="text-center">{{ trans('users.School') }}</th>
                        <td class="text-center">{{ $relation->school->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <th class="text-center">{{ trans('users.Birth Date') }}</th>
                        <td class="text-center">{{ $relation->birth_date ?? ''  }}</td>
                    </tr>
                @endif
                @if($row->type == \App\OurEdu\Users\UserEnums::STUDENT_TEACHER_TYPE)
                    <tr>
                        <th class="text-center">{{ trans('app.Country') }}</th>
                        <td class="text-center">{!!   $row->country->name ?? null !!}</td>
                    </tr>
                @endif
            <tr>
                <th class="text-center">{{ trans('users.Profile Picture') }}</th>
                <td class="text-center">{!! viewImage($row->profile_picture, 'large') !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('users.Is active') }}</th>
                <td class="text-center">{!!  $row->is_active ? '<span class="label label-primary">'.trans('app.active') : '<span class="label label-danger">'.trans('app.not active') !!}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
