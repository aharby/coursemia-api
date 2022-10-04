@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    @if (count($assessments))
        <a href="{{ route('school-branch-supervisor.result-viewers.assessments.index.exports',request()->query->all()) }}"
           target="_blank" class="btn btn-success">{{ trans('app.Export') }}</a>
    @endif
@endsection

@section('content')
    <div class="row">

        @if (!empty($assessments))
            @include('school_supervisor.reports.assessments._filter')
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">{{ trans('assessment.name') }}</th>
                                        <th class="text-center">{{ trans('assessment.starting date') }}</th>
                                        <th class="text-center">{{ trans('assessment.starting time') }}</th>
                                        <th class="text-center">{{ trans('assessment.finishing date') }}</th>
                                        <th class="text-center">{{ trans('assessment.finishing time') }}</th>
                                        <th class="text-center">{{ trans('assessment.assessor_type') }}</th>
                                        <th class="text-center">{{ trans('assessment.assessee_type') }}</th>
                                        <th class="text-center">{{ trans('assessment.avg_score') }}</th>
                                        <th class="text-center">{{ trans('assessment.assessed_assesses_count') }}</th>
                                        <th class="text-center">{{ trans('assessment.total_assesses_count') }}</th>
                                        <th class="text-center">{{ trans('assessment.actions') }}</th>
                                    </tr>
                                </thead-dark>
                                <tbody>
                                    @foreach ($assessments as $assessment)
                                        <tr class="text-center">
                                            <td>{{ $assessment->title ?? '' }}</td>
                                            <td>{{ date('d-m-Y', strtotime($assessment->start_at)) }}</td>
                                            <td>{{ date('H:i', strtotime($assessment->start_at)) }}</td>
                                            <td>{{ date('d-m-Y', strtotime($assessment->end_at)) }}</td>
                                            <td>{{ date('H:i', strtotime($assessment->end_at)) }}</td>
                                            <td>{{ $assessment->assessor_type ? trans('app.' . $assessment->assessor_type) : '' }}</td>
                                            <td>{{ $assessment->assessee_type ? trans('app.' . $assessment->assessee_type) : '' }}</td>
                                            <td>{{ $assessment->average_total_mark ? number_format(($assessment->average_score / $assessment->average_total_mark)*100, 2) : 0 }}%</td>
                                            <td>{{$assessment->pivot->assessed_assesses_count}}</td>
                                            <td>{{$assessment->pivot->total_assesses_count}}</td>
                                            <td>
                                                <a class="btn btn-primary btn-xs"
                                                    href="{{ route('school-branch-supervisor.result-viewers.assessments.assessors.view', ['assessment' => $assessment->id]) }}"
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
                {{ $assessments->links() }}
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
    </script>
@endpush
