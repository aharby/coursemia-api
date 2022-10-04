@extends('layouts.school_admin_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')

    <a href="{{ route('school-admin.formative-test.create', array_merge(["branch" => $branch ?? null], request()->all())) }}"
       class="btn btn-success">{{ trans('app.Create') }}</a>
      @endsection

@section('content')
    <div class="row">

        @if(!empty($formativeTests))

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">{{ trans('quiz.type') }}</th>
                                        <th class="text-center">{{ trans('quiz.name') }}</th>
                                        <th class="text-center">{{ trans('quiz.subject') }}</th>
                                         <th class="text-center">{{ trans('quiz.started_at') }}</th>
                                        <th class="text-center">{{ trans('quiz.ended_at') }}</th>
                                        <th class="text-center">{{ trans('quiz.publishing Status') }}</th>
                                        <th class="text-center">{{ trans('app.Is active') }}</th>

                                          <th class="text-center">{{ trans('app.actions') }}</th>
                                    </tr>
                                </thead-dark>
                                <tbody>
                                @foreach($formativeTests as $quiz)
                                    <tr class="text-center">
                                        <td>{{ trans("general_quizzes." . $quiz->quiz_type) }}</td>
                                        <td>{{ $quiz->title }}</td>
                                        <td>{{ $quiz->subject->name ??''}}</td>

                                        <td>{{ $quiz->start_at ??''}}</td>
                                        <td>{{ $quiz->end_at ??''}}</td>
                                        <td>
                                            @if(!is_null($quiz->published_at))
                                                <span class="badge-success">{{ trans("app.Published") }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ trans("app.Not Published") }}</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if($quiz->is_active)
                                                <span class="badge-success">{{ trans("app.active") }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ trans("app.not active") }}</span>
                                            @endif
                                        </td>
                                           <td>

                                            @if($quiz->quiz_type == \App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum::FORMATIVE_TEST && !$quiz->published_at)
                                            <a class="btn btn-primary btn-xs" href="{{ route('school-admin.formative-test.edit', $quiz->id) }}"
                                                   title="{{trans('quiz.edit')}}">
                                                    <i class="mdi mdi-eye"></i>
                                                    {{trans('formative_tests.edit')}}
                                                </a>
                                            @endif
                                            <a class="btn btn-primary btn-xs"
                                               href="{{ route('school-admin.formative-test.get.clone', $quiz->id) }}"
                                               title="{{trans('quiz.clone')}}">
                                                <i class="fas fa-clone"></i>                                                {{trans('formative_tests.clone')}}
                                            </a>
                                            @if($quiz->quiz_type == \App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum::FORMATIVE_TEST)
                                            <a class="btn btn-primary btn-xs" href="{{ route('school-admin.formative-test.questions', [$quiz->id]) }}"
                                               title="{{trans('general_quizzes.questions')}}">
                                                <i class="mdi mdi-eye"></i>
                                                {{trans('general_quizzes.questions')}}
                                            </a>
                                            @endif
                                        @if(
                                                $quiz->quiz_type == \App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum::FORMATIVE_TEST
                                                and !$quiz->students_answered_count
                                            )
                                        <a class="btn btn-primary btn-xs" href="{{ route('school-admin.formative-test.publish', $quiz->id) }}"
                                               title="{{trans('quiz.publish')}}">
                                               @if (!$quiz->published_at)
                                               <i class="mdi mdi-eye"></i>
                                               {{trans('formative_tests.publish')}}
                                               @else
                                               <i class="mdi mdi-eye"></i>
                                               {{trans('formative_tests.unpublish')}}
                                               @endif

                                        </a>

                                    @endif
                                            @if($quiz->quiz_type == \App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum::FORMATIVE_TEST)
                                                <button data-toggle="modal" data-target="#confirm-delete_{{$quiz->id}}"
                                                    class="btn btn-xs btn-danger confirm">
                                                    {{ trans('formative_tests.delete') }}
                                                </button>
                                                <div class="modal fade" id="confirm-delete_{{$quiz->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h5>   {{trans('app.Are you sure you want to delete this item')}}</h5>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('app.cancel')}}</button>
                                                                <form method="POST" class="" action="{{route('school-admin.formative-test.delete', $quiz->id)}}">
                                                                    {{ csrf_field() }}
                                                                    {{ method_field('DELETE') }}
                                                                    <button class="btn btn-danger btn-ok"> {{ trans('formative_tests.delete') }}</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            @endif

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
                {{ $formativeTests->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
@push('scripts')
    <script>



    </script>
@endpush
