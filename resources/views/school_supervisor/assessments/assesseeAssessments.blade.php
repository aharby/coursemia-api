@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('content')
    <div class="row">

        @if (!empty($assessmentUsers))
            @include('school_supervisor.assessments._filter')
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">{{trans('assessment.serial_number')}}</th>
                                        <th class="text-center">{{ trans('assessment.name') }}</th>
                                        <th class="text-center">{{ trans('assessment.date') }}</th>
                                        <th class="text-center">{{ trans('assessment.avg_score') }}</th>
                                        <th class="text-center">{{ trans('assessment.actions') }}</th>
                                    </tr>
                                </thead-dark>
                                <tbody>
                                    @php
                                        $pageNumber = request()->query('page') ? request()->query('page') - 1 : 0;
                                        $serial = $pageNumber * env('PAGE_LIMIT', 15) + 1;
                                    @endphp
                                    @foreach ($assessmentUsers as $assessmentUser)
                                        <tr class="text-center">
                                            <td>{{ $serial++ }}</td>
                                            <td>{{ $assessmentUser->assessment->title ?? '' }}</td>
                                            <td>{{ date('d-m-Y', strtotime($assessmentUser->assessment->start_at)) }}</td>
                                            <td>{{ $assessmentUser->avg_total_mark > 0 ?  number_format(($assessmentUser->avg_score / $assessmentUser->avg_total_mark)*100, 2) : 0.00 }}%</td>

                                            <td>
                                                <a class="btn btn-primary btn-xs"
                                                    href="{{ route('school-branch-supervisor.assessee.assessors.list', ['assessment' => $assessmentUser->assessment->id]) }}"
                                                    title="{{ trans('app.Details') }}">
                                                    {{ trans('app.Details') }}
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
@push('scripts')

    <script>
        $(document).ready(function () {
            $('#from_date').datepicker()
            $('#to_date').datepicker()
        });
    </script>
@endpush
