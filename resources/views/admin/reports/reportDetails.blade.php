@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('content')

    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>
            <tr>
                <th width="25%" class="text-center">{{ trans('reports.id') }}</th>
                <td width="75%" class="text-center">{{ $row->id }}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('reports.report') }}</th>
                <td width="75%" class="text-center">{{ $row->report }}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">{{ trans('reports.reportable_name') }}</th>

                @if($row->reportable_type == 'App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject')
                    <td width="75%" class="text-center">{{ $row->reportable->resource_slug ?? '' }}</td>
                @elseif($row->reportable_type == 'App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject')
                    <td width="75%" class="text-center">{{ $row->reportable->title ?? '' }}</td>
                @else($row->reportable_type == 'App\OurEdu\Subjects\Models\Subject')
                    <td width="75%" class="text-center">{{ $row->reportable->name ?? '' }}</td>

                @endif
            </tr>

            <tr>
                <th width="25%" class="text-center">{{ trans('reports.reportable_type') }}</th>
                <td width="75%" class="text-center">
                    {{ \App\OurEdu\Reports\ReportEnum::getAvailableTypesTrans($row->reportable_type) }}
                </td>
            </tr>

            <tr>
                <th width="25%" class="text-center">{{ trans('reports.student_name') }}</th>
                <td width="75%" class="text-center">{{ $row->student->user->name }}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">{{ trans('reports.created_on') }}</th>
                <td width="75%" class="text-center">{{ $row->created_at }}</td>
            </tr>


            </tbody>
        </table>
    </div>
@endsection
