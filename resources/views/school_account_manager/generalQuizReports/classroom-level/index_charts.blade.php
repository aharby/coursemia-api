@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('content')
    <div class="row">

        @if(!empty($generalQuizzes))
            @include('school_account_manager.generalQuizReports.classroom-level._filter')

            <tbody>
            @foreach($generalQuizzes as $quiz)
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-header">
                                <table class="table">
                                    <tr>
                                    <th class="text-center">{{ trans('quiz.Branch Name') }}</th>
                                    <th class="text-center">{{ trans('quiz.gradeClass') }}</th>
                                    <th class="text-center">{{ trans('quiz.classroom') }}</th>
                                    <th class="text-center">{{ trans('quiz.subject') }}</th>
                                    <th class="text-center">{{ trans('quiz.type') }}</th>
                                    <th class="text-center">{{ trans('quiz.name') }}</th>
                                    <th class="text-center">{{ trans('quiz.quiz date') }}</th>
                                    <th class="text-center">{{ trans('quiz.started_at') }}</th>
                                    <th class="text-center">{{ trans('quiz.ended_at') }}</th>
                                    </tr>
                                    <tr class="text-center">
                                        <td>{{ $quiz->branch->name ??''}}</td>
                                        <td>{{ $quiz->gradeClass->title ??''}}</td>
                                        <td>
                                            @foreach($quiz->classrooms as $classroom)
                                                ( {{ $classroom->name ??'' }} )
                                            @endforeach
                                        </td>
                                        <td>{{ $quiz->subject->name ??''}}</td>
                                        <td>{{ trans('quiz.'.$quiz->quiz_type) ??''}}</td>
                                        <td>{{ $quiz->title }}</td>
                                        <td>{{ \Carbon\Carbon::parse($quiz->start_at)->format("Y/m/d") ??''}}</td>
                                        <td>{{ \Carbon\Carbon::parse($quiz->start_at)->format("h:i a") ??''}}</td>
                                        <td>{{ \Carbon\Carbon::parse($quiz->end_at)->format("h:i a") ??''}}</td>
                                    </tr>
                                </table>
                            </div>


                            <div class="card-body">
                                <canvas id="bar-chart_{{$quiz->id}}" width="800" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
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

        $("#branchSelect").change(function () {
            $('#gradeClass_id').empty();
            $('#gradeClass_id').trigger('change')
            $('#gradeClass_id').append(`<option value="" selected> {{ __("quiz.gradeClass") }} </option>`);


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


        @if(request()->old('classroom_id'))
        $('#classroom_id').trigger('change');
        @endif

        $(document).ready(function () {
            $('#from_date').datepicker()
            $('#to_date').datepicker()
        });

        @foreach($generalQuizzes as $quiz)
            // Bar chart
            new Chart(document.getElementById("bar-chart_{{$quiz->id}}"), {
                type: 'bar',
                data: {
                    labels: ["{{ trans('quiz.count of quiz students') }}", "{{ trans('quiz.highest grade') }}", "{{ trans('quiz.lowest grade') }}", "{{ trans('general_quizzes.Average Score') }}"],
                    datasets: [
                        {
                            // label: "Population (millions)",
                            backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f", "#3c5a9f"],
                            data: [
                                "{{ $quiz->studentsAnswered->count() ?? 0 }}",
                                "{{ $quiz->highest_grade ?? 0 }}",
                                "{{ $quiz->lower_grade ?? 0 }}",
                                "{{ round($quiz->studentsAnswered->average('score_percentage'),2) ?? 0 }}"
                            ]
                        }
                    ]
                },
                options: {
                    legend: { display: false },
                    title: {
                        display: false,
                        text: 'Predicted world population (millions) in 2050'
                    }
                }
            });
        @endforeach

    </script>
@endpush
