@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@section('content')
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th width="15%" class="text-center">{{ trans('feedbacks.Student name') }}</th>
                    <th width="15%" class="text-center">{{ trans('feedbacks.Country') }}</th>
                    <th width="15%" class="text-center">{{ trans('feedbacks.School') }}</th>
                    <th class="text-center">{{ trans('feedbacks.Approved') }}</th>
                    <th width="20%" class="text-center">{{ trans('feedbacks.Feedback') }}</th>
                    <th class="text-center">{{ trans('feedbacks.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->student->user->getNameAttribute() }}</td>
                        <td>{{ $row->student->user->country->name ??''}}</td>
                        <td>{{ $row->student->school->name ??''}}</td>
                        <td>{!!  $row->approved ? '<span class="label label-primary">'.trans('feedbacks.Approved') : '<span class="label label-danger">'.trans('feedbacks.not approved') !!}</td>
                        <td>{{ $row->feedback }}</td>
                        <td>
                            <div class="row">
                                <div class="col-md-3 col-sm-3 col-xs-3 form-group">

                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                    <a class="btn btn-xs btn-primary"
                                       href="{{  route('admin.feedbacks.approve',$row->id) }}" data-toggle="tooltip"
                                       data-placement="top" data-title="{{ trans('feedbacks.approve') }}">
                                        <i class="fa fa-check"></i>
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                    <form method="POST" class=""
                                          action="{{route('admin.feedbacks.delete' , $row->id)}}">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button type="submit" class="btn btn-xs btn-danger" value="Delete Feedback"
                                                data-confirm="{{trans('app.Are you sure you want to delete this item')}}?">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3 form-group">

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
