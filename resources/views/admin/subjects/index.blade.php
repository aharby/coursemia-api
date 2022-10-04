@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">

        <div class="col-md-2 col-sm-2 col-xs-2" data-step="1" data-intro="{{trans('introJs.You Can Create subject!')}}"  data-position='right' >
            <a href="{{ route('admin.subjects.get.create') }}" class="btn btn-success">{{ trans('subjects.Create') }}</a>
        </div>

        <div class="col-md-2 col-sm-2 col-xs-2" data-step="2" data-intro="{{trans('introJs.The All Tasks Here!')}}"  data-position='right' >
            <a href="{{ route('admin.subjects.get.index.tasks') }}" class="btn btn-primary">{{ trans('subjects.All Tasks') }}</a>
        </div>

        <div class="col-md-2 col-sm-2 col-xs-2" data-step="3" data-intro="{{trans('introJs.export_subjects')}}"  data-position='right' >
            <a href="{{ route('admin.subjects.get.export') }}" class="btn btn-primary">{{ trans('subject.export') }}</a>
        </div>
    </div>
@endpush
@section('content')

    <label for="columns">{{trans('subject.sort')}}</label>
    <select name="sortby" id="sortSelector" onchange="window.location='{{route('admin.subjects.get.index')}}?sortby=' + this.value;">
        <option disabled selected value> {{trans('header.select_option')}} </option>
        <option value="name">{{ trans('subjects.Name') }}</option>
        <option value="practices_count">{{ trans('subjects.Practices Number') }}</option>
        <option value="exams_count">{{ trans('subjects.Number of exams') }}</option>
        <option value="average_result">{{ trans('subjects.average results') }}</option>
        <option value="created_at">{{ trans('subjects.created on') }}</option>
    </select>

    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('subjects.Name') }}</th>
                    <th class="text-center">{{ trans('subjects.Is active') }}</th>
                    <th class="text-center">{{ trans('subjects.is_top_qudrat') }}</th>
                    <th class="text-center">{{ trans('subjects.Practices Number') }}</th>
                    <th class="text-center">{{ trans('subjects.Number of exams') }}</th>
                    <th class="text-center">{{ trans('subjects.average results') }}</th>
                    <th class="text-center">{{ trans('subjects.created on') }}</th>
                    <th class="text-center">{{ trans('subjects.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->name }}</td>
                        <td>{!!  $row->is_active ? '<span class="label label-primary">'.trans('subjects.active') : '<span class="label label-danger">'.trans('subject.not active') !!}</td>
                        <td>{!!  $row->is_top_qudrat ? '<span class="label label-primary">'.trans('subjects.is_top_qudrat') : '<span class="label label-danger">'.trans('subjects.is_not_top_qudrat') !!}</td>
                        <td>{{ $row->practices_count }}</td>
                        <td>{{ $row->exams_count }}</td>
                        <td>{{ round($row->average_result,2) }}</td>
                        <td>{{ $row->created_at }}</td>
                        <td>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2 form-group">
                                    <a class="btn btn-xs btn-primary" href="{{  route('admin.subjects.get.view',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('subject.view') }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>

                            <div class="col-md-2 col-sm-2 col-xs-2 " data-step="3" data-intro="{{trans('introJs.Get Task of the subject')}}"  data-position='right' >
                                <div class="col-md-2 col-sm-2 col-xs-2 form-group">
                                    <a class="btn btn-xs btn-dark" href="{{  route('admin.subjects.get.subject.tasks',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('subject.tasks') }}">
                                        <i class="fa fa-tasks"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-2 col-xs-2 " data-step="4" data-intro="{{trans('introJs.You Can Edit subject!')}}"  data-position='right' >
                                <div class="col-md-2 col-sm-2 col-xs-2 form-group">
                                    <a class="btn btn-xs btn-info" href="{{  route('admin.subjects.get.edit',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('subject.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-2 col-xs-2 form-group">
                                <a class="btn btn-xs btn-success" href="{{  route('admin.subjects.get.logs',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('app.Logs') }}">
                                    <i class="fa fa-bar-chart"></i>
                                </a>
                            </div>


                                @if(!$row->is_aptitude)

                                    <div class="col-md-2 col-sm-2 col-xs-2 " data-step="5" data-intro="{{trans('introJs.You Can pause subject!')}}"  data-position='right' >
                                        <div class="col-md-2 col-sm-2 col-xs-2 form-group">
                                            @if((auth()->user()->type == \App\OurEdu\Users\UserEnums::SUPER_ADMIN_TYPE))
                                                <form method="POST" action="{{route('admin.subjects.pause.subject' , $row->id)}}">
                                                    {{ csrf_field() }}
                                                    <button type="submit" class="btn btn-xs btn-{{$row->is_active == 1 ? 'warning' : 'primary'}}"
                                                            value="Pause Subject" data-toggle="tooltip" data-placement="top" data-title="{{ trans('subject.pause') }}">
                                                        <i class="fa fa-{{$row->is_active == 1 ? 'pause' : 'play'}}"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-sm-2 col-xs-2 form-group">
                                        <form method="POST" class="" action="{{route('admin.subjects.delete' , $row->id)}}">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-xs btn-danger" value="Delete Station"
                                                    data-confirm="{{trans('app.Are you sure you want to delete this item')}}?">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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
