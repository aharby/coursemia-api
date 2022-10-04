
@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush

@section('title',@$page_title)

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    {{ Form::model($row, ['method' => 'put','class'=>'form-vertical form-label-left' ,  "enctype"=>"multipart/form-data"]) }}
                    @include('school_supervisor.classrooms.form',['special' => true])
                    <div class="form-group">
                        <div class="form-layout-footer">
                            <button type="submit" class="btn btn-success"> {{ trans('app.Save') }}</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div></div></div>
    {{--    </div>--}}
@endsection

