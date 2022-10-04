@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">

        <div class="col-md-2 col-sm-2 col-xs-2" data-step="1" data-intro="{{trans('introJs.You Can Create subject!')}}"  data-position='right' >
            <a href="{{ route('admin.certificates.thanking.create') }}" class="btn btn-success">{{ trans('subjects.Create') }}</a>
        </div>
    </div>
@endpush
@section('content')
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('subjects.ID') }}</th>
                    <th class="text-center">{{ trans('subjects.Image') }}</th>
                    <th class="text-center">{{ trans('subjects.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->id }}</td>
                        <td><img src="{{ $row->image }}" width="100px" height="100px"></td>
                        <td>
                            <div class="col-md-2 col-sm-2 col-xs-2 " data-step="3" data-intro="{{trans('introJs.Get Task of the subject')}}"  data-position='right' >
                                <div class="col-md-2 col-sm-2 col-xs-2 form-group">
                                    <a class="btn btn-xs btn-dark" target="_blank" href="{{ route('admin.certificates.thanking.demo', $row) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('subject.view') }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>
                            </div>


                            <div class="col-md-2 col-sm-2 col-xs-2 " data-step="4" data-intro="{{trans('introJs.You Can Edit subject!')}}"  data-position='right' >
                                <div class="col-md-2 col-sm-2 col-xs-2 form-group">
                                    <a class="btn btn-xs btn-info" href="{{  route('admin.certificates.thanking.edit',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('subject.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-2 col-xs-2 form-group">
                                <form method="POST" class="" action="{{route('admin.certificates.thanking.destroy', $row)}}">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button type="submit" class="btn btn-xs btn-danger" value="Delete Station"
                                            data-confirm="{{trans('app.Are you sure you want to delete this item')}}?">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>

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
