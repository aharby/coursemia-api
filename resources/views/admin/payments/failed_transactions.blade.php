@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('content')
    <div class="row">
        @include('admin.payments._filter')
        <div class="col-md-12 form-group">
        <a class="btn btn-md btn-success pull-right" href="{{route('admin.payments.export_failed_transactions',request()->query())}}" role="button"><i class="mdi mdi-delete-circle"></i> {{ trans('app.Export') }}</a>
        </div>
    @if(!$rows->isEmpty())
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('payment.Name') }}</th>
                    <th class="text-center">{{ trans('payment.amount') }}</th>
                    <th class="text-center">{{ trans('payment.reason for failure') }}</th>
                    <th class="text-center">{{ trans('payment.Product Type') }}</th>
                    <th class="text-center">{{ trans('payment.Product') }}</th>
                    <th class="text-center">{{ trans('payment.date') }}</th>

                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->sender?->name }}</td>
                        <td>{{ $row->amount }}</td>
                        <td>
                            {{$row->methodable ? \App\OurEdu\Payments\Enums\UrWayErrorCodesDescEnum::translatableKeys($row->methodable->response_code) : "---"}}
                        </td>
                        <td> {{ $row->detail?->subscribable ? resolveSubscribableName($row)['type'] : trans('payment.money added to wallet') }}</td>
                        <td> {{ $row->detail?->subscribable ? resolveSubscribableName($row)['name'] : $row->receiver?->name}} </td>
                        <td>{{\Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i:s')}}</td>
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
