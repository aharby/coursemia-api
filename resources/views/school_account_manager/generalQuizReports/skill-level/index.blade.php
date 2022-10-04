@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection
@section('buttons')
    @if (!empty($generalQuizzes))
        <a href="{{ route("school-account-manager.general-quizzes-reports.branch.reports.skill.levels.export", request()->query->all()) }}" class="btn btn-md btn-success align-right" target="_blank">{{ trans("app.Export") }}</a>
    @endif
@endsection
@section('content')
    <div class="row">

        @if(!empty($generalQuizzes))
            @include('school_account_manager.generalQuizReports.skill-level._filter')

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">{{ trans('quiz.Branch Name') }}</th>
                                        <th class="text-center">{{ trans('quiz.gradeClass') }}</th>
                                        <th class="text-center">{{ trans('quiz.classroom') }}</th>
                                        <th class="text-center">{{ trans('quiz.subject') }}</th>
                                        <th class="text-center">{{ trans('quiz.type') }}</th>
                                        <th class="text-center">{{ trans('quiz.name') }}</th>
                                        <th class="text-center">{{ trans('quiz.quiz date') }}</th>
                                        <th class="text-center">{{ trans('quiz.View score percentage for sections') }}</th>
                                        <th class="text-center"></th>
                                    </tr>
                                </thead-dark>
                                <tbody>
                                @foreach($generalQuizzes as $quiz)
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
                                        <td>
                                            <a class="btn btn-primary btn-xs" href="{{ route('school-account-manager.general-quizzes-reports.sectionPercentageReport', $quiz->id) }}"
                                               title="{{trans('quiz.view')}}">
                                                <i class="mdi mdi-eye"></i>
                                                {{trans('quiz.view')}}
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

        $("#classroom_id").change(function () {
            const subject = $('#subject_id');
            subject.empty();
            subject.append(`<option value="" selected> {{ trans("quiz.subject") }} </option>`);
            let classroom_id = $(this).val();
            $.get('{{ route('school-account-manager.manager-reports.get-classroom-subjects') }}',
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'classroom_id': $(this).val(),
                })
                .done(function (response) {
                    $.each(response.subjects, function (i, item) {
                        $('#subject_id').append(`<option value="${i}">${item} </option>`);
                    });
                });
        });


        @if(request()->old('subject'))
        $('#subject_id').trigger('change');
        @endif

        @if(request()->old('subject'))
            $('#subject_id').trigger('change');
        @endif

        $(document).ready(function () {
            $('#from_date').datepicker()
            $('#to_date').datepicker()
        });

    </script>
@endpush
