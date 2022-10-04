@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('content')
<div class="row">
    <a class="btn btn-primary btn pull-right" href="instructors/export?{{ request()->getQueryString()}}">{{ trans('instructors.export')}}</a>
</div>
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('instructors.Instructor Name') }}</th>
                    <th class="text-center">{{ trans('instructors.Country') }}</th>
                    <th class="text-center">{{ trans('instructors.Subject') }}</th>
                    <th class="text-center">{{ trans('instructors.total hours') }}</th>
                    <th class="text-center">{{ trans('instructors.Rate') }}</th>
                    <th class="text-center">{{ trans('users.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>
                            <a href="instructors/view/{{$row->id}}">{{ $row->user->name}}</a>
                        </td>
                        <td>{{ $row->user->country->name??'' }}</td>
                        <td>
                        @foreach($row->user->subjects as $subject)
                           <a href="subjects/view/{{$subject->id}}">{{ $subject->name }}</a>
                            <br>
                        @endforeach
                        </td>
                        <td>
                           {{ $row->instructor_total_hours }}
                        </td>
                        <td>
                            {{ round($row->user->ratings->avg('rating'),1) }}

                        </td>
                        <td>
                            <a class="btn btn-xs btn-primary" href="instructors/details/{{$row->id}}"
                               data-toggle="tooltip" data-placement="top" data-title="{{ trans('instructors.rating details') }}">
                                <i class="fa fa-eye"></i>
                            </a>
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
