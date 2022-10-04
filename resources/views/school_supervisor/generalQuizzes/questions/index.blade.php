@extends('layouts.school_manager_layout')
@section('title')
{{ @$page_title }}
@endsection

@section('content')
<div class="row">

    @if(!empty($questions))

    <div class="col-md-12 grid-margin stretch-card">

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead-dark>
                            <tr>
                                <th class="text-center">{{ trans('general_quizzes.Question') }}</th>
                                <th class="text-center">{{ trans('quiz.type') }}</th>
                                <th class="text-center">{{ trans('general_quizzes.Section') }}</th>
                                <th class="text-center">{{ trans('general_quizzes.Question Grade') }}</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead-dark>
                        <tbody>
                        @foreach($questions as $question)
                        <tr class="text-center">
                            @if($question->slug == "drag_drop_text" or $question->slug == "drag_drop_image")
                            <td>{!!  ($question->question->description ?? '') !!}</td>
                            @else
                                <td>{!!   $question->question->question ?? $question->question->text ?? '' !!}</td>
                            @endif

                            <td>{{ \App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums::getLabel($question->slug) }}</td>
                            <td>{{ $question->section->title ??''}}</td>
                            <td>{{ $question->grade ??''}}</td>

                            <td>{{ $question->homework_avg ??''}}</td>
                            <td>
                                @if(is_null($generalQuiz->published_at))
                                    @if(!$question->pivot->added_from_bank)
                                        <button class="btn btn-primary btn-xs"  href="javascript:void(0)" onclick=openCloseModal(true,`edit`,{{$question->id}})
                                                title="{{trans('app.Edit')}}">
                                            <i class="mdi mdi-eye"></i>
                                            {{trans('app.Edit')}}
                                        </button>
                                    @endif
                                    <a class="btn btn-danger btn-xs" href="{{route('school-branch-supervisor.general-quizzes.questions.delete', [$generalQuiz->id, $question->id])}}"
                                       title="{{trans('app.Delete')}}">
                                        <i class="mdi mdi-eye"></i>
                                        {{trans('app.Delete')}}
                                    </a>
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
            {{ $questions->links() }}
        </div>
    <div class="pull-right">
    </div>
    @else
    @include('partials.noData')
    @endif
        <div class="add_question">
            <div class="content">
                <div class="modal" tabindex="-1" role="dialog" id="add_question" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog" style="max-width: 80%; height: 70%" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ trans('app.questions') }}</h5>
                                <button type="button" class="btn btn-danger" onclick=openCloseModal(false,`add`)>x
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="errors-questions"></div>
                                <div class="inject-app"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
<div id="loader-wrapper" style="display: none;z-index: 2000">
    <div id="loader" style="right: 50%;left: 50%"></div>
    <div id="loader-content">
        {{ trans("api.Please wait ") }}
    </div>
</div>
@endsection
@section('buttons')
    @if($generalQuiz->is_active && is_null($generalQuiz->published_at))
        <button class="btn btn-dark add_question_modal mx-2" onclick=openCloseModal(true,`add`)>{{ trans("general_quizzes.add question") }}</button>
    @endif

    @if($questions->count() > 0)
        <button class="btn btn- btn-primary add_question_modal" onclick=openCloseModal(true,`view_as_student`)>{{ trans("general_quizzes.view as student") }}</button>
    @endif
@endsection

@push('scripts')
<script src="https://www.wiris.net/demo/plugins/app/WIRISplugins.js?viewer=image">
</script>
<script>
    function openCloseModal(show,operation,questionId = null){
        if (show === true){
            $('.inject-app').html(`<iframe
                                    id="iframe"
                                    class="w-100"
                                    height="500"
                                    src="{{ env('QUESTION_APP_URL') }}/ar?mainOperation=${operation}&type=homework&token=Bearer {{(app(\App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface::class))->createAuthToken(\App\OurEdu\Users\Auth\Enum\TokenNameEnum::DYNAMIC_lINKS_Token)}}&userType=instructor&lang={{app()->getLocale()}}"
                                    ></iframe>`)
            const iframe = document.getElementById("iframe");
            $("#loader-wrapper").show()


            iframe.onload = function() {
                $('#loader-wrapper').hide();
                let message = {mainObject:{id: {{$generalQuiz->id}}}};
                if (questionId !== null){
                    message.question = {id:questionId};
                }
                if (operation === `view_as_student`){
                    message = {
                        viewAsStudent: {
                            generalQuizId: {{$generalQuiz->id}},
                            generalQuizType: `homework`,
                        },
                    };
                }
                iframe.contentWindow.postMessage(message, "*");
                window.addEventListener("message", function(event) {
                    let data = event.data
                    switch (data.state) {
                        case "questionSubmittedSuccesfuly":
                            location.reload();
                            break;
                        case "questionEditedSuccesfuly":
                            location.reload();
                            break;
                        case "questionSubmittedWithError":
                            let errors = '';
                            data.response.errors.map((item) => {
                                errors += ' '+item.detail;
                            });
                            $('.errors-questions').html('<div class="alert alert-danger alert-dismissible">\n' +
                                '                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\n' +
                                '                    <h4><i class="icon fa fa-ban"></i>{{ trans("app.Something went wrong") }}</h4>\n' +
                                '                        <li>' + errors + '</li>\n' +
                                '                </div>')
                            break;
                        case "finishLoading":
                            this.finishLoadingFrame = data.response;
                            break;

                        default:
                            break;
                    }
                });
            }
            $('#add_question').show();
        }else {
            document.getElementById("iframe");
            $('#add_question').hide();
        }

    }

</script>
@endpush
@section('head')
<style>
    .table td img {
        border-radius: 0 !important;
    }
</style>
@endsection

