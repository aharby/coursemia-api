@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@section('content')
    <div class="row">
        @if(!empty($rows))
            @section('buttons')
                <div class="col-md-2 col-sm-2 col-xs-2" data-step="2" data-intro="Export Students" data-position='right'>
                    <a href="{{ route('school-branch-supervisor.subject-instructors.export.instructors.data', ["branch" => $branch ?? null]).'?'.request()->getQueryString() }}"
                       class="btn btn-primary">{{ trans('students.Export') }}</a>
                </div>
            @endsection
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="text-center">{{ trans('grade-class.instructor') }}</th>
                                    <th class="text-center">{{ trans('users.id') }}</th>
                                    <th class="text-center">{{ trans('grade-class.Subjects') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($rows as $row)
                                    <tr class="text-center">
                                        <td>{{ $row->name }}</td>
                                        <td>{{ $row->id }}</td>
                                        <td>
                                            <table>

                                                @foreach($row->schoolInstructorSubjects as $subject)
                                                    <tr>
                                                        <td>
                                                            id: {{  $subject->id }}
                                                            <br>
                                                            name: {{  $subject->name }}
                                                            <br>

                                                            educational_system: {{  $subject->educationalSystem->name ?? '' }}
                                                            <br>

                                                            grade class: {{  $subject->gradeClass->title  ?? ''}}
                                                            <br>

                                                            acadimc year: {{  $subject->academicalYears->title ?? '' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
{{--                {{ $rows->links() }}--}}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection

