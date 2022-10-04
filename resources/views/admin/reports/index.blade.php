@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('content')
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('reports.report') }}</th>
                    <th class="text-center">{{ trans('reports.reportable_id') }}</th>
                    <th class="text-center">{{ trans('reports.reportable_type') }}</th>
                    <th class="text-center">{{ trans('reports.student_name') }}</th>
                    <th class="text-center">{{ trans('reports.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->report }}</td>
                        @if($row->reportable_type == 'App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject')
                        <td>{{ $row->reportable->resource_slug ?? '' }}</td>
                        @elseif($row->reportable_type == 'App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject')
                        <td>{{ $row->reportable->title ?? '' }}</td>
                        @else($row->reportable_type == 'App\OurEdu\Subjects\Models\Subject')
                        <td>{{ $row->reportable->name ?? '' }}</td>

                        @endif
                        <td>
                            {{ \App\OurEdu\Reports\ReportEnum::getAvailableTypesTrans($row->reportable_type) }}
                        </td>
                        <td>{{ $row->student->user->name }}</td>

                        <td>
                            <a class="btn btn-xs btn-primary" href="reports/details/{{$row->id}}"
                               data-toggle="tooltip" data-placement="top" data-title="{{ trans('reports.report details') }}">
                                <i class="fa fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pull-right">
                {{ $rows->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
