@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('content')
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-striped dt-responsive nowrap">
                <tbody>

                @foreach($rows as $key => $row)

                    @if($key%2 == 0)
                        <tr class='text-center'>
                    @endif
                        <td><a href="{{ route("admin.grade.colors.assign.grades", $row) }}" ><img width="300px;" src="{{ url("storage/" . $row->image) }}"></a> </td>

                    @if($key%2 != 0)
                        <tr class='text-center'>
                    @endif
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
