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
    @if($row->total())
        <a href="{{ route('school-branch-supervisor.instructors-rates.get.export',$instructor) }}" target="_blank" class="btn btn-md btn-success align-right">{{ trans("app.Export") }}</a>
    @endif
@endsection

@section('title')
    {{ @$page_title }} - {{ $instructor->name }}  - {{ trans('instructors.Total Rates') }} #{{ $row->total() }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th class="text-center">{{ trans('instructors.Student Name') }}</th>
                                <th class="text-center">{{ trans('instructors.Comment') }}</th>
                                <th class="text-center">{{ trans('instructors.Rate') }}</th>
                                <th class="text-center">{{ trans('instructors.created on') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($row as $rate)
                                <tr class="text-center">
                                    <td>{{$rate->user->name}}</td>
                                    <td>{{$rate->comment}}</td>
                                    <td>{{$rate->rating}}</td>
                                    <td>{{$rate->created_at}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
