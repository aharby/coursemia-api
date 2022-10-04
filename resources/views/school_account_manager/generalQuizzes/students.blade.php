@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    @if(!empty($students))
        <div class="btn-group mr-2" role="group" >
            <a href="{{ route('school-account-manager.general-quizzes.students.export', $generalQuiz->id) }}"
               class="btn btn-success">{{ trans('app.Export') }}</a>
        </div>
        <div class="btn-group mr-2" role="group" >
            <a href="{{ route('school-account-manager.general-quizzes.grades.export', $generalQuiz->id) }}"
               class="btn btn-success">{{ trans('app.Export by questions grades') }}</a>
        </div>
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
                                    </tr>
                                </thead-dark>
                                <tbody>
                                @foreach($students as $student)
                                    <tr class="text-center">
                                        <td>{{ $student->name ??''}}</td>
                                        <td>{{ $student->username ??''}}</td>
                                        @php $status = $student->is_active ? (in_array($student->id,array_keys($student_answered))?$student_answered[$student->id] .'/'. $generalQuiz->mark :trans('general_quizzes.did not attend')) : trans('general_quizzes.inactive')   @endphp
                                        <td style="color: {{ !isset($student_answered[$student->id]) || (isset($student_answered[$student->id])  && $student_answered[$student->id] < 50) ? "red" : "green" }}">{{ $status }} </td>                                    </tr>
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
