@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('content')

    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>
            <tr>
                <th width="25%" class="text-center">@lang('instructors.Name')</th>
                <td width="75%" class="text-center">{{ $row->user->name }}</td>
            </tr>

            <tr>
                <th class="text-center">{{ trans('instructors.Subject')  }}</th>
                <td class="text-center">
                @foreach($row->user->subjects as $subject)
                  {{ $subject->name }}
                    <br>
                @endforeach
                </td>
            </tr>
            <tr>
               {{--
               TODO add number of student who request him to a virtual class
                + number of student who joined his scheduled class
                --}}
                <th width="25%" class="text-center">@lang('instructors.number of student')</th>
                <td width="75%" class="text-center">
                  {{$students_count}}
                </td>
            </tr>
            <tr>
                {{--
                TODO add total number of hours for this instructor
                 --}}
                <th width="25%" class="text-center">@lang('instructors.number of hours')</th>
                <td width="75%" class="text-center">
                </td>
            </tr>



            </tbody>
        </table>
    </div>
@endsection
