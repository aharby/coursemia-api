@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('content')
        @if($rows)
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                    <tr>
                        <th class="text-center">{{trans('translator.File name')}} </th>
                        <th class="text-center">{{trans('translator.Actions')}} </th>
                    </tr>
                </thead>
                <tbody>
                    @php $i=1; @endphp
                        @foreach ($rows as $row)
                        <tr>
                            <td width="25%" class="text-center">{{ucfirst($row)}}</td>
                            <td width="25%" class="text-center">
                                    <a class="btn btn-xs btn-info" href="{{  route('admin.translator.get.edit',$row) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('translator.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                            </td>
                        </tr>
                        @php $i++; @endphp
                    @endforeach
                </tbody>
            </table>
        @else
        @include('partials.noData')
        @endif
@endsection
