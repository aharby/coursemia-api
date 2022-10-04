@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    @if(!empty($students))
        <div class="btn-group mr-2" role="group" >
            <a href="{{ route('school-branch-supervisor.general-quizzes.students.export', $generalQuiz->id) }}"
               class="btn btn-success">{{ trans('app.Export') }}</a>
        </div>
            <div class="btn-group mr-2" role="group" >
            <a href="{{ route('school-branch-supervisor.general-quizzes.grades.export', $generalQuiz->id) }}"
               class="btn btn-success">{{ trans('app.Export by questions grades') }}</a>
        </div>
        @if( ($generalQuiz->quiz_type == \App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum::PERIODIC_TEST))

            <div class="btn-group mr-2" role="group" >
            @if($generalQuiz->show_result and $generalQuiz->quiz_type == \App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum::PERIODIC_TEST)
                <a href="{{route('school-branch-supervisor.general-quizzes.prevent.result-show.toggle.all', $generalQuiz->id)}}"
                   class="btn btn-danger ">
                    {{ trans('general_quizzes.prevented result show') }}
                </a>
            @elseif(!$generalQuiz->show_result and $generalQuiz->quiz_type == \App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum::PERIODIC_TEST)
                <a href="{{route('school-branch-supervisor.general-quizzes.prevent.result-show.toggle.all', $generalQuiz->id)}}"
                   class="btn  btn-primary ">
                    {{ trans('general_quizzes.Allow result show') }}
                </a>
            @endif
        </div>
        @endif

    @endif
@endsection

@section('content')
    <div class="row">
        @if(!empty($students))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">{{ trans('quiz.student name') }}</th>
                                        <th class="text-center">{{ trans('quiz.student ID') }}</th>
                                        <th class="text-center">{{ trans('quiz.result') }}</th>
                                        <th class="text-center">{{ trans('quiz.actions') }}</th>
                                    </tr>
                                </thead-dark>
                                <tbody>
                                @foreach($students as $student)
                                    <tr class="text-center">
                                        <td>{{ $student->name ??''}}</td>
                                        <td>{{ $student->username ??''}}</td>
                                        @php $status = $student->is_active ? (in_array($student->id,array_keys($student_answered))
                                                ?$student_answered[$student->id]['score'] .'/'.
                                                $generalQuiz->mark :trans('general_quizzes.did not attend')) : trans('general_quizzes.inactive')   @endphp
                                        <td style="color: {{ !isset($student_answered[$student->id]) || (isset($student_answered[$student->id])  && (!$student->is_active or $student_answered[$student->id]['score'] < 50)) ? "red" : "green" }}">{{ $status }} </td>
                                        @if(isset($student_answered[$student->id]) and $generalQuiz->quiz_type == \App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum::PERIODIC_TEST)
                                        <td>
                                            <a href="{{route('school-branch-supervisor.general-quizzes.prevent.result-show.toggle', $student_answered[$student->id]['id'])}}"
                                                class="btn btn-xs
                                                  {{ $student_answered[$student->id]['show_result'] ? ' btn-danger ' : ' btn-primary '}}

                                                   confirm">
                                                {{ $student_answered[$student->id]['show_result'] ? trans('general_quizzes.prevented result show') : trans('general_quizzes.Allow result show')}}
                                            </a>
                                        </td>
                                        @else
                                            <td></td>
                                        @endif
                                    </tr>
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
