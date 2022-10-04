
@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush

@section('title',@$page_title)

@section('content')

    <div class="row">
        @if(!empty($jobErrors))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap">
                                <thead>
                                <tr>
                                    <th class="text-center">{{ trans('Row') }}</th>
                                    <th class="text-center">{{ trans('Error') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($jobErrors as $error)

                                    <tr class="text-center">
                                        <td>{{ $error->row }}</td>
                                        <td> {!!  $error->error !!} </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                {{ $jobErrors->links() }}
            </div>
        @endif
    </div>

@endsection

