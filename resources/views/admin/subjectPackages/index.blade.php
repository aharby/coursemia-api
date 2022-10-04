@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <div class="col-md-2 col-sm-2 col-xs-2" data-step="1" data-intro="{{trans('introJs.You Can Create subject Packages!')}}"  data-position='right' >
        <a href="{{ route('admin.subjectPackages.get.create') }}" class="btn btn-success">{{ trans('subject_packages.Create') }}</a>
        </div>
    </div>
@endpush
@section('content')
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('subject_packages.Name') }}</th>
                    <th class="text-center">{{ trans('subject_packages.Country') }}</th>
                    <th class="text-center">{{ trans('subject_packages.Educational System') }}</th>
                    <th class="text-center">{{ trans('subject_packages.Academic year') }}</th>
                    <th class="text-center">{{ trans('subject_packages.Grade Class') }}</th>
                    <th class="text-center">{{ trans('subject_packages.Subjects') }}</th>
                    <th class="text-center">{{ trans('subject_packages.Price') }}</th>
                    <th class="text-center">{{ trans('subject_packages.Is Active') }}</th>
                    <th class="text-center">{{ trans('subject_packages.created_on') }}</th>
                    <th class="text-center">{{ trans('subject_packages.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->country->name }}</td>
                        <td>{{ $row->educationalSystem->name }}</td>
                        <td>{{ $row->academicalYears->title }}</td>
                        <td>{{ $row->gradeClass->title }}</td>
                        <td>
                           @foreach($row->subjects as $subject)
                                {{ $subject->name }} <br>
                            @endforeach
                        </td>
                        <td>{{ $row->price }} {{ trans('subject_packages.riyal') }}</td>
                        <td>{!!  $row->is_active ? '<span class="label label-primary">'.trans('users.active') : '<span class="label label-danger">'.trans('users.not active') !!}</td>
                        <td>{{ $row->created_at }}</td>
                        <td>
                            <div class="row">
                                <div class="col-md-3 col-sm-3 col-xs-3 " data-step="2" data-intro="{{trans('introJs.You Can view subject packages!')}}"  data-position='right' >
                                    <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                        <a class="btn btn-xs btn-primary" href="{{  route('admin.subjectPackages.get.view',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('users.view') }}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3 " data-step="3" data-intro="{{trans('introJs.You Can Edit subject packages!')}}"  data-position='right' >
                                    <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                        <a class="btn btn-xs btn-info" href="{{  route('admin.subjectPackages.get.edit',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('users.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3 " data-step="4" data-intro="{{trans('introJs.You Can View Log subject packages!')}}"  data-position='right' >
                                    <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                        <a class="btn btn-xs btn-success" href="{{  route('admin.subjectPackages.get.logs',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('app.Logs') }}">
                                            <i class="fa fa-bar-chart"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3 " data-step="5" data-intro="{{trans('introJs.You Can Delete subject packages!')}}"  data-position='right' >
                                    <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                        <form method="POST" class="" action="{{route('admin.subjectPackages.delete' , $row->id)}}">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-xs btn-danger" value="Delete Station"
                                                    data-confirm="{{trans('app.Are you sure you want to delete this item')}}?">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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
