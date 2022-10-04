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
                <th width="25%" class="text-center">{{ trans('contact.first name') }}</th>
                <td width="75%" class="text-center">{{ $row->first_name }}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('contact.last name') }}</th>
                <td width="75%" class="text-center">{{ $row->last_name }}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('contact.email') }}</th>
                <td width="75%" class="text-center">{{ $row->email }}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('contact.mobile') }}</th>
                <td width="75%" class="text-center">{{ $row->mobile }}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('contact.message') }}</th>
                <td width="75%" class="text-center">{{ $row->message }}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('contact.created on') }}</th>
                <td width="75%" class="text-center">{{ $row->created_at }}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
