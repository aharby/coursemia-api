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
                    <th class="text-center">{{ trans('reports.difficulty_level') }}</th>
{{--                    <th class="text-center">{{ trans('reports.difficulty_level_result') }}</th>--}}
                    <th class="text-center">{{ trans('reports.question_type') }}</th>
                    <th class="text-center">{{ trans('reports.is_ignored') }}</th>
                    <th class="text-center">{{ trans('reports.is_reported') }}</th>
                    <th class="text-center">{{ trans('reports.sme') }}</th>
                    <th class="text-center">{{ trans('reports.content_author') }}</th>
                    <th class="text-center">{{ trans('reports.created_on') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>
                             {{ $row->difficulty_level }} -> {{ $row->difficulty_level_result_equation }}
                        </td>
{{--                        <td>{{ $row->difficulty_level_result_equation }}</td>--}}
                        <td>{{ $row->slug }}</td>
                        <td>{!!  $row->is_ignored ? '<span class="label label-primary">'.trans('reports.true') : '<span class="label label-danger">'.trans('reports.false') !!}</td>
                        <td>{!!  $row->is_reported ? '<span class="label label-primary">'.trans('reports.true') : '<span class="label label-danger">'.trans('reports.false') !!}</td>
                        @if($row->is_reported==true)
                        <td>{{ $row->subject->sme->name  ?? ''}}</td>
                        <td>{{$row->questionReportTask->contentAuthors->first()->user->name ?? ''}}</td>
                        <td>{{$row->questionReportTask->created_at}}</td>
                         @else
                        <td>_</td>
                        <td>_</td>
                        <td>_</td>
                        @endif
{{--                        {{dd($row->questionReportTask->contentAuthors)}}--}}


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
