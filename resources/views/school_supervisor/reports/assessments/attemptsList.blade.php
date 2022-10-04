@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection


@section('content')
    <h5> {{ $assessment->title ?? '' }}</h5>

    <div class="row">

        @if (!empty($assessmentUsers))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">{{ trans('assessment.id') }}</th>
                                        <th class="text-center">{{ trans('assessment.the date') }}</th>
                                        <th class="text-center">{{ trans('assessment.the time') }}</th>
                                        <th class="text-center">{{ trans('assessment.score') }}</th>
                                        <th class="text-center">{{ trans('assessment.rate') }}</th>
                                        <th class="text-center">{{ trans('assessment.action') }}</th>
                                    </tr>
                                </thead-dark>
                                <tbody>

                                    @foreach ($assessmentUsers as $assessmentUser)
                                        <tr class="text-center">
                                            <td>{{$assessmentUser->id}}
                                            </td>
                                            <td>{{ date("d/m/Y", strtotime($assessmentUser->start_at)) }}
                                            </td>
                                            <td>{{ date("h:i a", strtotime($assessmentUser->start_at)) }}
                                            </td>
                                            <td>{{ number_format(($assessmentUser->score/$assessmentUser->total_mark)*100, 2) }}%
                                            </td>
                                            <td>{{ $assessmentUser->assessment->rates
                                                        ? $assessmentUser->assessment->rates->where('min_points', '<=', $assessmentUser->score)->
                                                        where('max_points', '>=', $assessmentUser->score)
                                                        ->first()->rate ?? ""
                                                        : '' }}
                                            </td>
                                            <td>
                                                <button class="btn btn-primary btn-xs"  href="javascript:void(0)" onclick=openCloseModal(true,`AnswerRreviewer`,`{{ $assessmentUser->id }}`)
                                                        title="{{trans('assessment.questions answers')}}">
                                                    <i class="mdi mdi-note-circle"></i>
                                                    {{trans('assessment.questions answers')}}
                                                </button>
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
                {{ $assessmentUsers->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif


            {{-- Modal of answer --}}
            <div class="answer">
                <div class="content">
                    <div class="modal" tabindex="-1" role="dialog" id="answer" data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog" style="max-width: 80%; height: 70%" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ $assessment->title }}</h5>
                                    <button type="button" class="btn btn-danger" onclick=openCloseModal(false,`add`)>x
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="errors-answers"></div>
                                    <div class="inject-app"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
@endsection


@push('scripts')
    <script>
        function openCloseModal(show,operation,endpointUrl){
            if (show === true){
                $('.inject-app').html(`<iframe
                                    id="iframe"
                                    class="w-100"
                                    height="500"
                                    src="{{ env('QUESTION_APP_URL') }}/ar?mainOperation=${operation}&type=assessment&token=Bearer {{(app(\App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface::class))->createAuthToken(\App\OurEdu\Users\Auth\Enum\TokenNameEnum::DYNAMIC_lINKS_Token)}}&userType=instructor&lang={{app()->getLocale()}}"
                                    ></iframe>`)
                const iframe = document.getElementById("iframe");
                $("#loader-wrapper").show()


                iframe.onload = function() {
                    $('#loader-wrapper').hide();
                    let message = {mainObject:{id:endpointUrl}};
                    iframe.contentWindow.postMessage(message, "*");
                    window.addEventListener("message", function(event) {
                        let data = event.data
                        switch (data.state) {
                            case "close":
                                location.reload();
                                break;
                            case "questionSubmittedWithError":
                                let errors = '';
                                data.response.errors.map((item) => {
                                    errors += ' '+item.detail;
                                });
                                $('.errors-answers').html('<div class="alert alert-danger alert-dismissible">\n' +
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
                $('#answer').show();
            }else {
                document.getElementById("iframe");
                $('#answer').hide();
            }

        }

    </script>
@endpush
