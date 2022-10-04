@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('content')
    <div>
        <strong> @lang('instructors.Instructor Name')</strong> : {{ $row->user->name }}

    </div>
    <div class="row">
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('instructors.Student Name') }}</th>
                    <th class="text-center">{{ trans('instructors.Comment') }}</th>
                    <th class="text-center">{{ trans('instructors.Rate') }}</th>
                    <th class="text-center">{{ trans('instructors.created on') }}</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($row->user->ratings as $rate)
                        <tr class="text-center">
                            <td>
                                {{$rate->user->name}}
                            </td>
                            <td>
                                {{$rate->comment}}
                            </td>
                            <td>
                                {{$rate->rating}}
                            </td>
                            <td>
                                {{$rate->created_at}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

    </div>
@endsection
