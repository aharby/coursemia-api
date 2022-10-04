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
                    <th class="text-center">{{ trans('users.Type') }}</th>
                    <th class="text-center">{{ trans('users.Name') }}</th>
                    <th class="text-center">{{ trans('users.username') }}</th>
                    <th class="text-center">{{ trans('users.School') }}</th>
                    <th class="text-center">supervisor account</th>
                    <th class="text-center">session data</th>
                    <th class="text-center">agora log id</th>
                    <th class="text-center">{{ trans('contact.message') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->user->type ?? '' }}</td>
                        <td>{{ $row->user->name ?? ''}}</td>
                        <td>{{ $row->user->username ?? ''}}</td>
                        <td>{{ $row->branch->name ?? ''}}</td>
                        <td>{{ $row->branch->supervisor->name ?? '' }} id {{ $row->branch->supervisor->username ?? '' }}</td>
                        <td>{{ $row->session_info }}</td>
                        <td>{{ $row->agora_log_id }}</td>
                        <td>{{ $row->message  }}</td>
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
