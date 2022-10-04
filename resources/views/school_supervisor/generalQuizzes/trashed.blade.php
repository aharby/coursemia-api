@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    @if(!empty($generalQuizzes))
        <a href="{{ route('school-branch-supervisor.general-quizzes.trashed.exports', ['classroomId' => request('classroomId')])}}"
           class="btn btn-success">{{ trans('app.Export') }}</a>
    @endif
@endsection

@section('content')
    <div class="row">

        @if(!empty($generalQuizzes))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">{{ trans('quiz.type') }}</th>
                                        <th class="text-center">{{ trans('quiz.name') }}</th>
                                        <th class="text-center">{{ trans('quiz.instructor') }}</th>
                                        <th class="text-center">{{ trans('quiz.subject') }}</th>
                                        <th class="text-center">{{ trans('quiz.started_at') }}</th>
                                        <th class="text-center">{{ trans('quiz.ended_at') }}</th>
                                        <th class="text-center">{{ trans('quiz.grade_average') }}</th>
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
                                        <td>{{ $quiz->start_at ??''}}</td>
                                        <td>{{ $quiz->end_at ??''}}</td>
                                        <td>{{ $quiz->homework_avg ?? 0 }} / {{ $quiz->mark }}</td>
                                        <td>
                                            <a class="btn btn-primary btn-xs"
                                               href="{{ route('school-branch-supervisor.general-quizzes.students.trashed', $quiz->id) }}"
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
                {{ $generalQuizzes->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
