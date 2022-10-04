@extends('layouts.school_admin_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    @if (count($assessmentUsers))
        <a href="{{  route('school-admin.assessments.assessees.view.exports', ['assessment' => $assessment->id, 'assessor' => $assessor->id])  }}"
           target="_blank" class="btn btn-success">{{ trans('app.Export') }}</a>
    @endif
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
                                        <th class="text-center">{{ trans('assessment.assessee_name') }}</th>
                                        <th class="text-center">{{ trans('assessment.score') }}</th>
                                        <th class="text-center">{{ trans('assessment.action') }}</th>
                                    </tr>
                                </thead-dark>
                                <tbody>
                                    @foreach ($assessmentUsers as $assessmentUser)
                                        <tr class="text-center">
                                            <td>{{ $assessmentUser->assessee->name }}
                                            </td>
                                            <td>{{ $assessmentUser->total_mark > 0 ? number_format(($assessmentUser->score/$assessmentUser->total_mark)*100, 2) : 0}}%</td>
                                            <td>
                                                <a class="btn btn-primary btn-xs"
                                                   href="{{ route('school-admin.assessments.assessees.view.details', ['assessment' => $assessment->id, 'assessor' => $assessor, 'assessee' => $assessmentUser->assessee]) }}"
                                                   title="{{ trans('assessment.details') }}">
                                                    {{ trans('assessment.details') }}
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
                {{ $assessmentUsers->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
