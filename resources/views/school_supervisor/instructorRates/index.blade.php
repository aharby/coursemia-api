<style>

    #container {
        color: white;
        background: white;
        border: black;
        width: 100px;
        height: 50px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #content {
        background: #3e4b5b;
        /*height:30px;*/
        border-radius: 30px;
        width: 250px;


    }

</style>
@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush

@section('buttons')
    @if(count($rows))
        <a href="{{ route('school-branch-supervisor.instructors-rates.export.all', request()->all()) }}" target="_blank" class="btn btn-md btn-success align-right">{{ trans("app.Export") }}</a>
    @endif
@endsection

@section('title', @$page_title)
@section('content')
    <div class="row">
        @if(!empty($rows))
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th class="text-center">{{ trans('instructors.Instructor Name') }}</th>
                                <th class="text-center">{{ trans('instructors.Subject') }}</th>
                                <th class="text-center">{{ trans('instructors.Rate') }}</th>
                                <th class="text-center">{{ trans('app.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($rows as $row)
                                <tr class="text-center">
                                    <td>
                                        {{ $row->name}}
                                    </td>
                                    <td class="text-center">
                                        <div style="height: 100px;overflow: auto;overflow-x: hidden;">
                                            @foreach($row->schoolInstructorSubjects as $subject)
                                                <div id="content" style="background:#f67b95;  width: 175px; color: white;">
                                                    <b>{{$subject->name}}</b>
                                                </div>
                                                <div id="content" style="color: white">
                                                    {{  $subject->gradeClass->title  }}
                                                </div>
                                                <br>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        {{ round($row->ratings->avg('rating'),1) }}
                                    </td>
                                    <td>
                                        <a class="btn btn-xs btn-primary" href="{{ route('school-branch-supervisor.instructors-rates.get.view',$row->id) }}"
                                           data-toggle="tooltip" data-placement="top" data-title="{{ trans('app.View') }}">
                                            {{ trans('app.View') }}
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
