@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush
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
                    <th class="text-center">{{ trans('grade-class.Branch') }}</th>
                    <th class="text-center">{{ trans('grade-class.Educational System') }}</th>
                    <th class="text-center">{{ trans('grade-class.Academical Year') }}</th>
                    <th class="text-center">{{ trans('grade-class.Educational Term') }}</th>
                    <th class="text-center">{{ trans('grade-class.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->branch->name ?? '' }} </td>
                        <td>{{ $row->educationalSystem->name ?? '' }} </td>
                        <td>{{ $row->academicYear->title ?? ''}} </td>
                        <td>{{ $row->educationalTerm->title ?? ''}} </td>
                        <td>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2 " data-step="2" data-intro="{{trans('introJs.You Can View Courses!')}}"  data-position='right' >
                                    <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                        <a class="btn btn-xs btn-primary"
                                           href="{{  route('school-branch-supervisor.grade-classes.get.subjects',[$gradeClass->id,$row->id, $branch->id ?? null]) }}"
                                           data-toggle="tooltip" data-placement="top"
                                           data-title="{{ trans('grade-class.view') }}">
                                            {{ trans('grade-class.Subjects') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
                        </div></div></div></div>
            <div class="pull-right">
                {{--                {{ $rows->links() }}--}}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
