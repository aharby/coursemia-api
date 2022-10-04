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
                    <th class="text-center">{{ trans('contact.first name') }}</th>
                    <th class="text-center">{{ trans('contact.last name') }}</th>
                    <th class="text-center">{{ trans('contact.email') }}</th>
                    <th class="text-center">{{ trans('contact.created on') }}</th>
                    <th class="text-center">{{ trans('countries.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->first_name }}</td>
                        <td>{{ $row->last_name }}</td>
                        <td>{{ $row->email }}</td>
                        <td>{{ $row->created_at }}</td>
                        <td>
                            <div class="row">
                                <div class="form-group">
                                    <a class="btn btn-xs btn-primary" href="{{  route('admin.contact.get.view',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('app.view') }}">
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
