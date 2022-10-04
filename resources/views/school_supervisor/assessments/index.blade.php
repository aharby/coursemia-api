@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('content')
    <div class="row">

        @if (!empty($assessments))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">{{ trans('assessment.name') }}</th>
                                        <th class="text-center">{{ trans('assessment.date') }}</th>
                                        <th class="text-center">{{ trans('assessment.assessor_type') }}</th>
                                        <th class="text-center">{{ trans('assessment.assessee_type') }}</th>
                                        <th class="text-center">{{ trans('assessment.actions') }}</th>
                                    </tr>
                                </thead-dark>
                                <tbody>
                                    @foreach ($assessments as $assessment)
                                        <tr class="text-center">
                                            <td>{{ $assessment->title ?? '' }}</td>
                                            <td>{{ date('d-m-Y', strtotime($assessment->start_at)) }}</td>
                                            <td>{{ $assessment->assessor_type ? trans('app.' . $assessment->assessor_type) : '' }}
                                            </td>
                                            <td>{{ $assessment->assessee_type ? trans('app.' . $assessment->assessee_type) : '' }}
                                            </td>

                                            <td>
                                                <a class="btn btn-primary btn-xs"
                                                    href="{{ route('school-branch-supervisor.assessor.assessments.assessees.list', ['assessment' => $assessment->id]) }}"
                                                    title="{{ trans('assessment.assess') }}">
                                                    {{ trans('assessment.assess') }}
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
