@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('content')
    <div class="row">
        @if(!empty($rows))
        @include('school_supervisor.educationalSupervisor._filter',['username'=>true])
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead-dark>
                                <tr>
                                    <th class="text-center">{{ trans('educationalSupervisor.ID') }}</th>
                                    <th class="text-center">{{ trans('educationalSupervisor.name') }}</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead-dark>
                            <tbody>
                            @foreach($rows as $row)
                                <tr class="text-center">
                                    <td>{{ $row->username ?? '' }}</td>
                                    <td>{{ $row->name ??''}}</td>

                                        <td>
                                            <!-- @if(can('view-students')) -->
                                                <a class="btn btn-primary btn-xs" href="{{ route('school-branch-supervisor.educational-supervisors.get.view', $row->id) }}"
                                                   title="{{trans('app.View')}}">
                                                    {{trans('app.View')}}
                                                </a>
                                            <!-- @endif -->

                                            <a class="btn btn-xs btn-info"
                                               href="{{ route('school-branch-supervisor.educational-supervisors.get.edit', ["educational_supervisor" => $row->id, "branch" => $branch ?? null]) }}"
                                               data-toggle="tooltip" data-placement="top"
                                               data-title="{{ trans('educationalSupervisor.edit') }}">
                                                {{ trans('app.edit') }}
                                            </a>
                                        </td>
                                    </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="pull-right">
            {{ $rows->links() }}
        </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
