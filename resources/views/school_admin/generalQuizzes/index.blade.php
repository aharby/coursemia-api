@extends('layouts.school_admin_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    @if(!empty($generalQuizzes))
        <a href="{{ route('school-admin.general-quizzes.index.export', array_merge(["branch" => $branch ?? null], request()->all())) }}"
           class="btn btn-success">{{ trans('app.Export') }}</a>
    @endif


@endsection

@section('content')
    <div class="row">

        @if(!empty($generalQuizzes))
            @include('school_admin.generalQuizzes._filter')

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">{{ trans('quiz.type') }}</th>
                                        <th class="text-center">{{ trans('quiz.time') }}</th>
                                        <th class="text-center">{{ trans('quiz.instructor') }}</th>
                                        <th class="text-center">{{ trans('quiz.subject') }}</th>
                                        <th class="text-center">{{ trans('quiz.classroom') }}</th>
                                        <th class="text-center">{{ trans('quiz.started_at') }}</th>
                                        <th class="text-center">{{ trans('quiz.ended_at') }}</th>
                                        <th class="text-center">{{ trans('quiz.publishing Status') }}</th>
                                        <th class="text-center">{{ trans('app.Is active') }}</th>
                                        <th class="text-center">{{ trans('general_quizzes.Average Score') }}</th>
                                        <th class="text-center"></th>
                                    </tr>
                                </thead-dark>
                                <tbody>
                                @foreach($generalQuizzes as $quiz)
                                    <tr class="text-center">
                                        <td>{{ $quiz->quiz_type ??''}}</td>
                                        <td>{{ $quiz->title }}</td>
                                        <td>{{ $quiz->creator->name ??''}}</td>
                                        <td>{{ $quiz->subject->name ??''}}</td>
                                        <td>
                                            @foreach($quiz->classrooms->pluck('name') as $classroom)
                                               <div>{{$classroom}}</div>
                                            @endforeach
                                                </td>
                                        <td>{{ $quiz->start_at ??''}}</td>
                                        <td>{{ $quiz->end_at ??''}}</td>
                                        <td>
                                            @if(!is_null($quiz->published_at))
                                                <span class="badge-success">{{ trans("app.Published") }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ trans("app.Not Published") }}</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if($quiz->is_active)
                                                <span class="badge-success">{{ trans("app.active") }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ trans("app.not active") }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $quiz->homework_avg ?? 0 }} / {{ $quiz->mark }}</td>
                                        <td>
                                            <a class="btn btn-primary btn-xs" href="{{ route('school-admin.general-quizzes.students', $quiz->id) }}"
                                               title="{{trans('quiz.students')}}">
                                                <i class="mdi mdi-eye"></i>
                                                {{trans('quiz.students')}}
                                            </a>
                                            @if($quiz->quiz_type !== \App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum::QUIZ)
                                                <button data-toggle="modal" data-target="#confirm-delete_{{$quiz->id}}"
                                                    class="btn btn-xs btn-danger confirm">
                                                    {{ trans('general_quizzes.delete') }}
                                                </button>
                                                <div class="modal fade" id="confirm-delete_{{$quiz->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h5>   {{trans('app.Are you sure you want to delete this item')}}</h5>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('app.cancel')}}</button>
                                                                <form method="POST" class="" action="{{route('school-admin.general-quizzes.delete', $quiz->id)}}">
                                                                    {{ csrf_field() }}
                                                                    {{ method_field('DELETE') }}
                                                                    <button class="btn btn-danger btn-ok"> {{ trans('general_quizzes.delete') }}</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            @endif

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
                {{ $generalQuizzes->links() }}
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

            {{--if (!$(this).val() ||  quizType === '{{ \App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum::PERIODIC_TEST }}') {--}}
            {{--    hideFields();--}}
            {{--} else {--}}
            {{--    showFields();--}}
            {{--    $('#gradeClass_id').trigger('change');--}}
            {{--}--}}
        })

        $("#branchSelect").change(function () {
            $('#gradeClass_id').empty();
            $('#gradeClass_id').trigger('change')
            $('#gradeClass_id').append(`<option value="" selected> {{ __("quiz.grade") }} </option>`);


            let branch_id = $(this).val();

            $.get('{{ route('school-branch-supervisor.classrooms.branches.gradeClass') }}' + `/${branch_id}`,
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                })
                .done(function (response) {
                    $.each(response.gradeClasses, function (i, item) {
                        $('#gradeClass_id').append(`<option value="${i}">${item} </option>`);
                    });
                });
        });

        $("#gradeClass_id").change(function () {
            $('#classroom_id').empty();
            $('#classroom_id').trigger("change");
            $('#classroom_id').append(`<option value="" selected> {{ __("quiz.classroom") }} </option>`);


            let gradeClass_id = $(this).val();
            let branch_id = $("#branchSelect").val();

            $.get('{{ route('school-branch-supervisor.classrooms.gradeClass.Classroom') }}' + `/${gradeClass_id}/${branch_id}`,
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
            {{--            @if( !request()->filled("quiz_type") or request()->get("quiz_type") === \App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum::PERIODIC_TEST )--}}
            {{--                hideFields()--}}
            {{--            @endif--}}

            $('#from_date').datepicker()
            $('#to_date').datepicker()
        });

    </script>
@endpush
