@extends('layouts.school_admin_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    @if(!empty($generalQuizzes))
        <a href="{{ route('school-admin.general-quizzes-reports.formative-tests-reports.export', array_merge(["branch" => $branch ?? null], request()->all())) }}"
           class="btn btn-success">{{ trans('app.Export') }}</a>
    @endif
@endsection

@section('content')
    <div class="row">
        @if(!empty($generalQuizzes))

        @include('school_admin.GeneralQuizzesReports.formative_test.filter')

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">{{ trans('quiz.title') }}</th>
                                        <th class="text-center">{{ trans('quiz.time') }}</th>
                                        <th class="text-center">{{ trans('quiz.subject') }}</th>
                                        <th class="text-center">{{ trans('quiz.gradeClass') }}</th>
                                        <th class="text-center">{{ trans('quiz.started_at') }}</th>
                                        <th class="text-center">{{ trans('quiz.ended_at') }}</th>
                                        <th class="text-center">{{ trans('app.Is active') }}</th>
                                        <th class="text-center">{{ trans('general_quizzes.Average Score') }}</th>
                                        <th class="text-center"></th>
                                    </tr>
                                </thead-dark>
                                <tbody>
                                @foreach($generalQuizzes as $quiz)
                                    <tr class="text-center">
                                        <td>{{ $quiz->title ??''}}</td>
                                        <td>{{ $quiz->test_time/60}}</td>
                                        <td>{{ $quiz->subject->name ??''}}</td>
                                        <td>
                                           {{$quiz->gradeClass->title}}
                                                </td>
                                        <td>{{ $quiz->start_at ??''}}</td>
                                        <td>{{ $quiz->end_at ??''}}</td>
                                        <td>
                                            @if($quiz->is_active)
                                                <span class="badge-success">{{ trans("app.active") }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ trans("app.not active") }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $quiz->homework_avg ?? 0 }} / {{ $quiz->mark }}</td>
                                        <td>
                                            <a class="btn btn-primary btn-xs" href="{{ route('school-admin.general-quizzes-reports.formative-tests-reports.students', [$quiz->id]) }}"
                                               title="{{trans('quiz.students')}}">
                                                <i class="mdi mdi-eye"></i>
                                                {{trans('quiz.students')}}
                                            </a>
{{--                                                <a class="btn btn-primary btn-xs" href="{{ route('school-admin.general-quizzes.questions', [$quiz->id , 'formative-tests-reports']) }}"--}}
{{--                                                       title="{{trans('general_quizzes.preview')}}">--}}
{{--                                                    <i class="mdi mdi-eye"></i>--}}
{{--                                                    {{trans('general_quizzes.preview')}}--}}
{{--                                                </a>--}}
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

        $(document).ready(function () {

            $('#from_date').datepicker()
            $('#to_date').datepicker()
        });

        $("#schoolSelect").change(function () {
            const branch = $('#branchSelect');
            branch.empty();
            branch.append(`<option value="" selected> {{ trans("app.School Account Branches") }} </option>`);
            let school_id = $(this).val();
            $.get('{{ route('school-admin.general-quizzes-reports.formative-tests-reports.getSchoolBranches') }}'+'/'+school_id,
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                })
                .done(function (response) {
                    $.each(response.data, function (i, item) {
                        branch.append(`<option value="${i}">${item} </option>`);
                    });
                });
        });
    </script>
@endpush
