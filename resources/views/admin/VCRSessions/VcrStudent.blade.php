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
                    <th class="text-center">{{ trans('VCRSessions.Student Name') }}</th>
                    <th class="text-center">{{ trans('VCRSessions.Subject Name') }}</th>
                    <th class="text-center">{{ trans('VCRSessions.Instructor Name') }}</th>
                    <th class="text-center">{{ trans('VCRSessions.Is active') }}</th>
                    <th class="text-center">{{ trans('VCRSessions.price') }}</th>
                    <th class="text-center">{{ trans('VCRSessions.created on') }}</th>
                    <th class="text-center">{{ trans('VCRSessions.ended at') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>
                            <a href="../users/view/{{$row->student->user->id ?? null}}">{{ $row->student->user->name ?? null }}</a>
                        </td>

                        <td>
                            <a href="../subjects/view/{{$row->subject->id ?? null }}">{{ $row->subject->name ?? null }}</a>
                        </td>

                        <td>
                            <a href="../users/view/{{$row->instructor->user->id ?? null}}">{{ $row->instructor->user->name ?? null }}</a>
                        </td>

                        <td>{!!  $row->is_active ? '<span class="label label-primary">'.trans('vcr_schedule.active') : '<span class="label label-danger">'.trans('vcr_schedule.not active') !!}</td>
                        <td>{{ $row->price }} {{trans('subject_packages.riyal')}}</td>
                        <td>{{ $row->created_at }}</td>
                        <td>{{ $row->ended_at ?? '' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
