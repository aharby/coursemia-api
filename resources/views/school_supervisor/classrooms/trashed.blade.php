@extends('layouts.school_manager_layout')
@section('title', @$page_title)

@section('content')
    <div class="row">
        @if(!empty($rows))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap">
                                <thead>
                                <tr>
                                    <th class="text-center">{{ trans('classrooms.Name') }}</th>
                                    <th class="text-center">{{ trans('classrooms.branch') }}</th>
                                    <th class="text-center">{{ trans('classrooms.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($rows as $row)
                                    <tr class="text-center">
                                        <td>{{ $row->name }}</td>
                                        <td>{{ $row->branch->name }}</td>
                                        <td>
                                            <a class="btn btn-xs btn-primary"
                                               href="{{  route('school-branch-supervisor.general-quizzes.index.trashed', ['classroomId' => $row->id]) }}"
                                               data-toggle="tooltip" data-placement="top"
                                               data-title="{{ trans('classrooms.reports') }}">
                                                {{ trans('classrooms.reports') }}
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

