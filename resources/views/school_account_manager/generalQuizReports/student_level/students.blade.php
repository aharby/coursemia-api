@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    @if(count($students))
        <a href="{{ route("school-account-manager.general-quizzes-reports.student.reports.student.levels.export", request()->query->all()) }}" target="_blank" class="btn btn-md btn-success align-right" style="margin: 10px;">{{ trans("app.Export") }}</a>
    @endif
@endsection

@section('content')
    <div class="row">
        @if(!empty($students))

        @include('school_account_manager.generalQuizReports.student_level._filter')
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead-dark>
                                <tr>
                                    <th class="text-center">{{ trans('app.School Account Branches') }}</th>
                                    <th class="text-center">{{ trans('students.grade class') }}</th>
                                    <th class="text-center">{{ trans('students.classroom') }}</th>
                                    <th class="text-center">{{ trans('students.ID') }}</th>
                                    <th class="text-center">{{ trans('students.student name') }}</th>
                                    <th class="text-center">{{trans('students.action')}}</th>
                                </tr>
                            </thead-dark>
                            <tbody>
                            @foreach($students as $student)
                                <tr class="text-center">
                                    @if($student->user)
                                        <td>{{ $student->classroom->branch->name ??''}}</td>
                                        <td>{{ $student->gradeClass->title ??'' }}</td>
                                        <td>{{ $student->classroom->name ??'' }}</td>
                                        <td>{{ $student->user->username ?? '' }}</td>
                                        <td>{{ $student->user->name ??''}}</td>
                                        <td>
                                            <a class="btn btn-xs btn-info"
                                               href="{{ route('school-account-manager.general-quizzes-reports.student.level.student.quizzes', ["student" => $student->user->id]). '?'.$filters }}"
                                               data-toggle="tooltip" data-placement="top"
                                               data-title="{{ trans('general_quizzes.details') }}">
                                                {{ trans('general_quizzes.details') }}
                                            </a>
                                        </td>
                                </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="pull-right">
            {{ $students->links() }}
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
