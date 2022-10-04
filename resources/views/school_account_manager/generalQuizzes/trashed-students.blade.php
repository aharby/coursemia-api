@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    @if(!empty($students))
        <div class="btn-group mr-2" role="group">
            <a href="{{ route('school-branch-supervisor.general-quizzes.students.export', $generalQuiz->id) }}"
               class="btn btn-success">{{ trans('app.Export') }}</a>
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
                                        <td style="color: {{ !isset($student_answered[$student->id]) || (isset($student_answered[$student->id])  && $student_answered[$student->id]['score'] < 50) ? "red" : "green" }}">{{ in_array($student->id,array_keys($student_answered))?$student_answered[$student->id]['score'] .'/'. $generalQuiz->mark :trans('general_quizzes.did not attend') }} </td>
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
