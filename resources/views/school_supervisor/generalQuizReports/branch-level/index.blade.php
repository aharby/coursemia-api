@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    @if(count($generalQuizzes))
        <a href="{{ route("school-branch-supervisor.general-quizzes-reports.student.reports.branch.levels.export", request()->query->all()) }}" target="_blank" class="btn btn-md btn-success align-right" style="margin: 10px;">{{ trans("app.Export") }}</a>
    @endif
@endsection

@section('content')
    <div class="row">

        @if (!empty($generalQuizzes))
            @include('school_supervisor.generalQuizReports.branch-level._filter')

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">{{ trans('quiz.Branch Name') }}</th>
                                        <th class="text-center">{{ trans('quiz.subject') }}</th>
                                        <th class="text-center">{{ trans('quiz.type') }}</th>
                                        <th class="text-center">{{ trans('quiz.name') }}</th>
                                        <th class="text-center">{{ trans('quiz.quiz date') }}</th>
                                        <th class="text-center">{{ trans('quiz.started_at') }}</th>
                                        <th class="text-center">{{ trans('quiz.ended_at') }}</th>

                                        <!-- <th class="text-center">{{ trans('quiz.publishing Status') }}</th> -->
                                        <!-- <th class="text-center">{{ trans('app.Is active') }}</th> -->
                                        <th class="text-center">{{ trans('general_quizzes.Average Score') }}</th>
                                        <th class="text-center">{{ trans('app.actions') }}</th>
                                    </tr>
                                </thead-dark>
                                <tbody>
                                    @foreach ($generalQuizzes as $quiz)
                                        <tr class="text-center">
                                            <td>{{ $quiz->branch->name ?? '' }}</td>
                                            <td>{{ $quiz->subject->name ?? '' }}</td>
                                            <td>{{ $quiz->quiz_type ?? '' }}</td>
                                            <td>{{ $quiz->title }}</td>
                                            <td>{{ \Carbon\Carbon::parse($quiz->start_at)->format('Y/m/d') ?? '' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($quiz->start_at)->format('h:i a') ?? '' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($quiz->end_at)->format('h:i a') ?? '' }}</td>


                                            <td>{{ round($quiz->studentsAnswered->average('score_percentage'), 2) ?? 0 }}
                                                %</td>
                                            <td>
                                                <a class="btn btn-primary btn-xs"
                                                    href="{{ route('school-branch-supervisor.general-quizzes-reports.branch-level-report.students', $quiz->id) }}"
                                                    title="{{ trans('quiz.students') }}">
                                                    <i class="mdi mdi-eye"></i>
                                                    {{ trans('quiz.students') }}
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
            $('#subject_id').empty();
            $('#subject_id').trigger("change");
            $('#subject_id').append(`<option value="" selected> {{ __('quiz.subject') }} </option>`);


            let gradeClass_id = $(this).val();
            let branch_id = "{{ $branch->id }}";

            $.get('{{ route('school-account-manager.manager-reports.gradeClass.subjects') }}' +
                    `/${gradeClass_id}/${branch_id}`, {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        'gradeClass': $(this).val(),
                    })
                .done(function(response) {
                    $.each(response.subjects, function(i, item) {
                        $('#subject_id').append(`<option value="${i}">${item} </option>`);
                    });
                });
        });


        @if (request()->old('subject'))
            $('#subject_id').trigger('change');
        @endif

        $(document).ready(function() {
            $('#from_date').datepicker()
            $('#to_date').datepicker()
        });

    </script>
@endpush
