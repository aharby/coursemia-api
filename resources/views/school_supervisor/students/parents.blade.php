@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('content')
    <div class="row">
        @if(!empty($rows))
            @section('buttons')
                <div class="col-md-2 col-sm-2 col-xs-2" data-step="2" data-intro="Export Students"  data-position='right' >
                    <a href="{{ route('school-branch-supervisor.students.get.export-parents', ["branch" => $branch ?? null]).'?'.request()->getQueryString() }}" class="btn btn-primary">{{ trans('students.Export') }}</a>
                </div>
            @endsection
            @include('school_supervisor.students._filter',['username'=>true,'mobile' => true,'classroom' => true])

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="text-center">{{ trans('students.student name') }}</th>
                                    <th class="text-center">{{ trans('parents.parent name') }}</th>
                                    <th class="text-center">{{ trans('parents.username') }}</th>
                                    <th class="text-center">{{ trans('parents.password') }}</th>
                                    <th class="text-center">{{ trans('parents.created on') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($rows as $row)
                                    @if($row->user)
                                        @foreach($row->user->parents as $parent)
                                            <tr class="text-center">
                                                <td>{{ $row->user->first_name ??''.' '.$row->user->last_name ??''}}</td>
                                                <td>{{ $parent->first_name.' '.$parent->last_name }}</td>
                                                <td>{{ $parent->username }}</td>
                                                <td>{{ $parent->parentData->password ?? '' }}</td>
                                                <td>{{ $row->created_at }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div></div></div></div>
            <div class="pull-right">
                {{ $rows->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
