@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('content')
    <div class="row">
        @if(!empty($quizzes))
            @include('school_account_manager.quizzes._filter')

            <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead-dark>
                                <tr>
                                    <th class="text-center">{{ trans('quiz.type') }}</th>
                                    <th class="text-center">{{ trans('quiz.Branch Name') }}</th>
                                    <th class="text-center">{{ trans('quiz.classroom') }}</th>
                                    <th class="text-center">{{ trans('quiz.name') }}</th>
                                    <th class="text-center">{{ trans('quiz.subject') }}</th>
                                    <th class="text-center">{{ trans('quiz.started_at') }}</th>
                                    <th class="text-center">{{ trans('quiz.ended_at') }}</th>
                                    <th class="text-center">{{ trans('quiz.grade_average') }}</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead-dark>
                            <tbody>
                            @foreach($quizzes as $quiz)
                                <tr class="text-center">
                                        <td>{{ $quiz->quiz_type == \App\OurEdu\Quizzes\Enums\QuizTypesEnum::QUIZ ? \App\OurEdu\Quizzes\Enums\QuizTimesEnum::getLabel($quiz->quiz_time) : \App\OurEdu\Quizzes\Enums\QuizTypesEnum::getLabel($quiz->quiz_type)}}</td>
                                        <td>{{ $quiz->branch->name }}</td>
                                        <td>{{ $quiz->classroom->name ??''}}</td>
                                        <td>{{ $quiz->quiz_title ??''}}</td>
                                        <td>{{ $quiz->subject->name ??''}}</td>
                                        <td>{{ $quiz->start_at ??''}}</td>
                                        <td>{{ $quiz->end_at ??''}}</td>
                                        <td>{{ $quiz->success_percentage ??''}}</td>
                                        <td>
                                            <a class="btn btn-primary btn-xs" href="{{ route('school-account-manager.school.manager.quiz.students', $quiz) }}"
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
            {!! $quizzes->withQueryString()->links()!!}
        </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
@push('scripts')
    <script>

        $('#branchSelect').change(function(e) {
            console.log("subject")
            let branch_id = $(this).val();
            $('#subjects').empty();
            $('#subjects').append(`<option value="" selected> {{ trans('reports.subject') }} </option>`);
            $.get('{{ route('school-account-manager.manager-reports.get-branch-subjects') }}/' + branch_id,
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                })
                .done(function (response) {
                    $.each(response.subjects, function (i, item) {
                        $('#subjects').append('<option value="'+i+'">'+item+'</option>');
                    });
                });
        });
        $(document).ready(function () {
            $('#from_date').datepicker({
                maxDate: 0
            });
            $('#to_date').datepicker({
                maxDate: 0
            });

            if (!("{{ request()->get('quizType') }}" === "{{ \App\OurEdu\Quizzes\Enums\QuizTimesEnum::PRE_SESSION }}" || "{{ request()->get('quizType') }}" === "{{ \App\OurEdu\Quizzes\Enums\QuizTimesEnum::AFTER_SESSION }}")) {
                $('#creatorContainer').hide()
                $('#subjectsContainer').hide()
                $('#created_by').val("")
                $('#subjects').val("")
            }
        });

        $("#branchSelect").change(function () {
            console.log("instructor")
            const instructor = $('#created_by');
            instructor.empty();
            instructor.append(`<option value="" selected> {{ trans("quiz.creator") }} </option>`);
            let branch_id = $(this).val();
            $.get('{{ route('school-account-manager.manager-reports.get-branch-quiz-creators') }}' + `/${branch_id}`,
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                })
                .done(function (response) {
                    console.log(response)
                    $.each(response.instructors, function (i, item) {
                        instructor.append(`<option value="${item.id}">${item.first_name} ${item.last_name} (${item.type}) </option>`);
                    });
                });
        });

        $("#quizType").change(function () {
            const instructorContainer = $('#creatorContainer');
            const subjectsContainer = $('#subjectsContainer');

            const instructor = $('#created_by');
            const subjects = $('#subjects');

            if (!($(this).val()  === "{{ \App\OurEdu\Quizzes\Enums\QuizTimesEnum::PRE_SESSION }}" || $(this).val()  === "{{ \App\OurEdu\Quizzes\Enums\QuizTimesEnum::AFTER_SESSION }}")) {
                instructor.val("")
                instructorContainer.hide()
                subjects.val("")
                subjectsContainer.hide()
            } else {
                instructorContainer.show()
                subjectsContainer.show()
            }
        });

    </script>
@endpush
