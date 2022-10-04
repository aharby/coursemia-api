@extends('layouts.school_admin_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    @if (count($assessmentAssessors))
        <a href="{{ route('school-admin.assessments.assessors.view.exports', ['assessment' => $assessment->id]) }}"
           target="_blank" class="btn btn-success">{{ trans('app.Export') }}</a>
    @endif
@endsection

@section('content')
    <h5> {{ $assessment->title ?? '' }}</h5>

    <div class="row">

        @if (!empty($assessmentAssessors))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">{{ trans('assessment.assessor_name') }}</th>
                                        <th class="text-center">{{ trans('assessment.branch') }}</th>
                                        <th class="text-center">{{ trans('assessment.avg_score') }}</th>
                                        <th class="text-center">{{ trans('assessment.action') }}</th>
                                    </tr>
                                </thead-dark>
                                <tbody>
                                    @foreach ($assessmentAssessors as $assessmentAssessor)
                                        <tr class="text-center">
                                            <td>{{ $assessmentAssessor->user->first_name . ' ' . $assessmentAssessor->user->last_name }}
                                            </td>
                                            <td>{{ $assessmentAssessor->user->branch ? $assessmentAssessor->user->branch->name : '' }}
                                            </td>
                                            <td>{{ $assessmentAssessor->average_total_mark > 0 ? number_format(($assessmentAssessor->average_score / $assessmentAssessor->average_total_mark) * 100, 2) : 0 }}%</td>
                                            <td>
                                                <a class="btn btn-primary btn-xs"
                                                    href="{{ route('school-admin.assessments.assessees.view', ['assessment' => $assessment->id, 'assessor' => $assessmentAssessor->user->id]) }}"
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
                {{ $assessmentAssessors->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
