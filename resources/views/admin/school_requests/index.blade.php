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
                    <th class="text-center">{{ trans('school-request.school name') }}</th>
                    <th class="text-center">{{ trans('school-request.number of students') }}</th>
                    <th class="text-center">{{ trans('school-request.manager name') }}</th>
                    <th class="text-center">{{ trans('school-request.manager mobile') }}</th>
                    <th class="text-center">{{ trans('school-request.manager email') }}</th>
                    <th class="text-center">{{ trans('school-request.status') }}</th>
                    <th class="text-center">{{ trans('school-request.created on') }}</th>
                    <th class="text-center">{{ trans('school-request.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->school_name }}</td>
                        <td>{{ $row->number_of_students }}</td>
                        <td>{{ $row->manager_name }}</td>
                        <td>{{ $row->manager_mobile }}</td>
                        <td>{{ $row->manager_email }}</td>
                        <td>{!!  $row->status == 'Approved' ? '<span class="label label-primary">'.trans('app.Approved Successfully') : '<span class="label label-danger">'.trans('feedbacks.not approved') !!}</td>
                        <td>{{ $row->created_at }}</td>
                        <td>
                            <div class="row">
                            @if($row->status != 'Approved')
                                <div class="col-md-2 col-sm-2 col-xs-2 " data-step="4" data-intro="{{trans('introJs.You Can Approve school request!')}}"  data-position='right' >
                                    <div class="col-md-2 col-sm-2 col-xs-2 form-group">
                                        <a class="btn btn-xs btn-info" href="{{  route('admin.school-requests.approve',$row->id) }}" data-toggle="tooltip" data-placement="top">
                                            {{ trans('feedbacks.approve') }}
                                        </a>
                                    </div>
                                </div>
                            @endif
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
