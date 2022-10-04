@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('content')
    <div class="row">
        @if (!$rows->isEmpty())
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                    <tr>
                        <th class="text-center">{{ trans('vcr_schedule.Instructor Name') }}</th>
                        <th class="text-center">{{ trans('vcr_schedule.From') }}</th>
                        <th class="text-center">{{ trans('vcr_schedule.To') }}</th>
                        <th class="text-center">{{ trans('vcr_schedule.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr class="text-center">
                            <td>{{ $row->instructor->name ?? null }}</td>
                            <td>{{ $row->from_date }}</td>
                            <td>{{ $row->to_date }}</td>
                            <td>
                                <div class="row">
                                    <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                        <a class="btn btn-xs btn-primary"
                                            href="{{ route('admin.vcr_schedules.get.view', $row->id) }}"
                                            data-toggle="tooltip" data-placement="top"
                                            data-title="{{ trans('vcr_schedule.view') }}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                        <a class="btn btn-xs btn-primary"
                                            href="{{ route('admin.vcr-sessions.vcr-sessions.schedules.attendance', $row->id) }}"
                                            data-toggle="tooltip" data-placement="top"
                                            data-title="{{ trans('vcr_schedule.sessions') }}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
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
