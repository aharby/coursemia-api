@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@section('buttons')
    <a href="{{url('instructors-sample.xls')}}" class="btn btn-primary">{{ trans('app.Excel Sample file') }}</a>
@endsection
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
                                    <th class="text-center">{{ trans('grade-class.Subject') }}</th>
                                    <th class="text-center">{{ trans('grade-class.import Instructors ') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($rows as $row)
                                    <tr class="text-center">
                                        <td>{{ $row->name }} </td>
                                        <td>
                                            @if(can('update-subjects'))
                                                <form method="POST" class="d-inline" enctype="multipart/form-data"
                                                      action="{{route('school-branch-supervisor.subject-instructors.import' , [$row->id, $branch->id ?? null])}}">
                                                    {{ csrf_field() }}
                                                    <input type="file" name="file" required id="file">
                                                    <label>{{ trans('app.Please use the attached file') }}</label>
                                                    <button type="submit" value="Upload file"
                                                    >{{ trans('app.Save') }}</button>
                                                </form>
                                            @endif
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
