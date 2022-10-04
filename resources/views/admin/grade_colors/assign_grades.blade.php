@extends('layouts.admin_layout')

@push('title')
    {{ @$page_title }}
@endpush

@section('title',@$page_title)

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 x_panel">
            {{ Form::model($row,['method' => 'put','class'=>'form-vertical form-label-left']) }}

            @foreach($rows as $index => $row)
                <div class="row mg-t-20 form-group">
                    <label for="{{$row->id}}" class="control-label col-md-2 col-sm-2 col-xs-12" for="name" >
                        {{ $row->title }}
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="checkbox" {{ !in_array($row->id, $selectedRows) ?: "checked" }}  id="{{$row->id}}"   name="color_grades[]" value="{{ $row->id }}">
                    </div>
                </div>
            @endforeach

            <div class="form-group">
                <div class="form-layout-footer">
                    <button type="submit" class="btn btn-success"> {{ trans('app.Save') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection

