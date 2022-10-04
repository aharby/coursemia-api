@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('content')
    <div class="row">
        @if(!empty($quizzes))
            @include('school_supervisor.quizzes._filter')

            <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead-dark>
                                <tr>
                                    <th class="text-center">{{ trans('quiz.type') }}</th>
                                    <th class="text-center">{{ trans('quiz.time') }}</th>
                                    <th class="text-center">{{ trans('quiz.classroom') }}</th>
                                    <th class="text-center">{{ trans('quiz.subject') }}</th>
                                    <th class="text-center">{{ trans('quiz.start time') }}</th>
                                    <th class="text-center">{{ trans('quiz.ended_at') }}</th>
                                    <th class="text-center">{{ trans('general_quizzes.Average Score') }}</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead-dark>
                            <tbody>
                            @foreach($quizzes as $quiz)
                                <tr class="text-center">
                                        <td>{{ $quiz->quiz_type ??''}}</td>
                                        <td>{{ $quizTimes::getLabel($quiz->quiz_time) ??''}}</td>
                                        <td>{{ $quiz->classroom->name ??''}}</td>
                                        <td>{{ $quiz->subject->name ??''}}</td>
                                        <td>{{ $quiz->start_at ??''}}</td>
                                        <td>{{ $quiz->end_at ??''}}</td>
                                        <td>{{ $quiz->success_percentage ??''}}</td>
                                        <td>
                                            <a class="btn btn-primary btn-xs" href="{{ route('school-branch-supervisor.quiz.students', $quiz) }}"
                                               title="{{trans('quiz.students')}}">
                                                <i class="mdi mdi-eye"></i>
                                                {{trans('quiz.students')}}
                                            </a>
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
            {{ $quizzes->links() }}
        </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
@push('scripts')
    <script>

        function hideFields () {
            const IDs = ["classroom_id",
                "instructor_id",
                "session_id"
            ];

            IDs.forEach(function (item) {
                let selector = "#" + item;
                $(selector).closest(".col-4").hide();
                $(selector).empty();
            })
        }

        function showFields () {
            const IDs = ["classroom_id",
                "instructor_id",
                "session_id"
            ];

            IDs.forEach(function (item) {
                $("#" + item).closest(".col-4").show();
            })
        }

        $("#quizType").change(function () {

            const quizType = $(this).val()

            if (!$(this).val() ||  quizType === '{{ \App\OurEdu\Quizzes\Enums\QuizTypesEnum::PERIODIC_TEST }}') {
                hideFields();
            } else {
                showFields();
                $('#gradeClass_id').trigger('change');
            }
        })


        $("#gradeClass_id").change(function () {
            $('#classroom_id').empty();
            $('#classroom_id').append(`<option value="" selected> {{ __("quiz.classroom") }} </option>`);


            let gradeClass_id = $(this).val();

            $.get('{{ route('school-branch-supervisor.classrooms.gradeClass.Classroom') }}' + `/${gradeClass_id}`,
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'gradeClass': $(this).val(),
                })
                .done(function (response) {
                    $.each(response.classrooms, function (i, item) {
                        $('#classroom_id').append(`<option value="${i}">${item} </option>`);
                    });
                });
        });

        $("#classroom_id").change(function () {
            const instructor = $('#instructor_id');
            instructor.empty();
            instructor.append(`<option value="" selected> {{ trans("quiz.instructor") }} </option>`);
            let classroom_id = $(this).val();
            $.get('{{ route('school-branch-supervisor.classrooms.getInstructor') }}' + `/${classroom_id}`,
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'classroom': $(this).val(),
                })
                .done(function (response) {
                    $.each(response.instructors, function (i, item) {
                        instructor.append(`<option value="${item.id}">${item.first_name} ${item.last_name} </option>`);
                    });
                });
        });
        @if(request()->old('classroom_id'))
            $('#classroom_id').trigger('change');
        @endif

        $("#instructor_id").change(function () {
            updateSessions()
        })

        $("#date").change(function () {
            updateSessions()
        })

        function updateSessions()
        {
            const sessionSelector = $('#session_id');
            const classroomSelector = $("#classroom_id");
            const dateSelector = $("#date");
            const instructorSelector = $("#instructor_id");

            sessionSelector.empty();
            sessionSelector.append(`<option value="" selected> {{ trans("quiz.session") }} </option>`);

            $.get('{{ route('school-branch-supervisor.classrooms.instructor.sessions') }}',
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'instructor': instructorSelector.val(),
                    'classroom': classroomSelector.val(),
                    'date': dateSelector.val(),
                })
                .done(function (response) {
                    $.each(response.sessions, function (i, item) {
                        sessionSelector.append(`<option value="${item.id}">${item.subject.name} -  ${item.from_date} - (${item.from_time} - ${item.to_time}) </option>`);
                    });
                });
        }

        @if(request()->old('classroom_id'))
        $('#classroom_id').trigger('change');
        @endif

        $(document).ready(function () {
            @if( !request()->filled("quizType") or request()->get("quizType") === \App\OurEdu\Quizzes\Enums\QuizTypesEnum::PERIODIC_TEST )
                hideFields()
            @endif

            $('#date').datepicker()
        });

    </script>
@endpush
