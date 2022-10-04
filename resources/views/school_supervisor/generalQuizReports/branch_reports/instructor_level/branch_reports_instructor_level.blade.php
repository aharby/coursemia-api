@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    <a href="{{ route("school-branch-supervisor.general-quizzes-reports.branch.reports.instructor.levels.charts", array_merge(["branch" => $branch ?? null], request()->all())) }}" class="btn btn-md btn-success align-right" style="margin: 10px;">{{ trans("general_quizzes.view chart") }}</a>
    @if(count($generalQuizzes))
        <a href="{{ route("school-branch-supervisor.general-quizzes-reports.branch.reports.instructor.levels.export", request()->query->all()) }}" target="_blank" class="btn btn-md btn-success align-right" style="margin: 10px;">{{ trans("app.Export") }}</a>
    @endif
@endsection

@section('content')
    <div class="row">

        @if (!empty($generalQuizzes))
            @include('school_supervisor.generalQuizReports.branch_reports.instructor_level._filter')

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">{{ trans('quiz.instructor') }}</th>
                                        <th class="text-center">{{ trans('quiz.gradeClass') }}</th>
                                        <th class="text-center">{{ trans('quiz.subject') }}</th>
                                        <th class="text-center">{{ trans('quiz.type') }}</th>
                                        <th class="text-center">{{ trans('quiz.name') }}</th>
                                        <th class="text-center">{{ trans('quiz.time') }}</th>
                                        <th class="text-center">{{ trans('quiz.started_at') }}</th>
                                        <th class="text-center">{{ trans('quiz.ended_at') }}</th>
                                        <th class="text-center">{{ trans('general_quizzes.Attend Students') }}</th>
                                        <th class="text-center">{{ trans('general_quizzes.highest grade') }}</th>
                                        <th class="text-center">{{ trans('general_quizzes.lower grade') }}</th>
                                        <th class="text-center">{{ trans('general_quizzes.Average Score') }}</th>
                                    </tr>
                                </thead-dark>
                                <tbody>
                                    @foreach ($generalQuizzes as $quiz)
                                        <tr class="text-center">
                                            <td>{{ $quiz->creator->name ?? '' }}</td>
                                            <td>{{ $quiz->gradeClass->title ?? '' }}</td>
                                            <td>{{ $quiz->subject->name ?? '' }}</td>
                                            <td>{{ trans('general_quizzes.' . $quiz->quiz_type) }}</td>
                                            <td>{{ $quiz->title }}</td>
                                            <td>{{ \Carbon\Carbon::parse($quiz->start_at)->format('Y/m/d') ?? '' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($quiz->start_at)->format('h:i a') ?? '' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($quiz->end_at)->format('h:i a') ?? '' }}</td>
                                            <td>{{ $quiz->studentsAnswered->count() ?? 0 }}</td>
                                            <td>{{ $quiz->highest_grade ?? 0.0 }}</td>
                                            <td>{{ $quiz->lower_grade ?? 0.0 }}</td>
                                            <td>{{ $quiz->successful_percentage ?? 0.0 }}</td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                {{ $generalQuizzes->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
@push('scripts')
    <script>
        $("#gradeClass_id").change(function() {
            $('#classroom_id').empty();
            $('#classroom_id').trigger("change");
            $('#classroom_id').append(`<option value="" selected> {{ __('quiz.classroom') }} </option>`);


            let gradeClass_id = $(this).val();
            let branch_id = "{{ $branch->id }}";

            $.get('{{ route('school-branch-supervisor.classrooms.gradeClass.Classroom') }}' +
                    `/${gradeClass_id}/${branch_id}`, {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        'gradeClass': $(this).val(),
                    })
                .done(function(response) {
                    $.each(response.classrooms, function(i, item) {
                        $('#classroom_id').append(`<option value="${i}">${item} </option>`);
                    });
                });
        });

        $("#classroom_id").change(function() {
            const instructor = $('#instructor_id');
            instructor.empty();
            instructor.append(`<option value="" selected> {{ trans('quiz.instructor') }} </option>`);
            let classroom_id = $(this).val();
            $.get('{{ route('school-branch-supervisor.classrooms.getInstructor') }}' + `/${classroom_id}`, {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'classroom': $(this).val(),
                })
                .done(function(response) {
                    $.each(response.instructors, function(i, item) {
                        instructor.append(
                            `<option value="${item.id}">${item.first_name} ${item.last_name} </option>`
                        );
                    });
                });
        });
        @if (request()->old('classroom_id'))
            $('#classroom_id').trigger('change');
        @endif

        $("#instructor_id").change(function() {
            const subject = $('#subject_id')
            subject.empty()
            subject.append(`<option value="" selected> {{ trans('quiz.subject') }} </option>`)
            let instructor_id = $(this).val()
            $.get('{{ route('school-branch-supervisor.classrooms.getInstructorSubjects') }}' +
                    `/${instructor_id}?classroom=` + $('#classroom_id').val() + `&gradeClass=` + $('#gradeClass_id')
                    .val(), {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        'instructor': $(this).val(),
                    })
                .done(function(response) {
                    $.each(response.subjects, function(i, item) {
                        subject.append(`<option value="${item.id}">${item.name}</option>`)
                    })
                })
        })
        @if (request()->old('instructor'))
            $('#instructor_id').trigger('change')
        @endif

        $(document).ready(function() {
            $('#from_date').datepicker()
            $('#to_date').datepicker()
        });

    </script>
@endpush
