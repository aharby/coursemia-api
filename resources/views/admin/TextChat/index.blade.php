@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        {{ Form::open(['route'=>'admin.textChat.createRoom','method' => 'post','class'=>'form-vertical form-label-left' , 'files' => false  ]) }}
        @include('form.input',['type'=>'text','name'=>'name',
        'attributes'=>['class'=>'form-control','label'=>trans('textChat.name'),'placeholder'=>trans('textChat.room name'),'required'=>'required']])

        <div class="form-group">
            <div class="form-layout-footer">
                <button type="submit" class="btn btn-success" > {{ trans('textChat.create room') }}</button>
            </div>
        </div>
        {{ Form::close() }}
    </div>

    <div class="row">
        {{ Form::open(['route'=>'admin.textChat.addUserToRoom','method' => 'post','class'=>'form-vertical form-label-left' , 'files' => false  ]) }}
        @include('form.input',['type'=>'text','name'=>'name',
        'attributes'=>['class'=>'form-control','label'=>trans('textChat.name'),'placeholder'=>trans('textChat.name'),'required'=>'required']])
        @include('form.input',['type'=>'text','name'=>'room',
               'attributes'=>['class'=>'form-control','label'=>trans('textChat.room name'),'placeholder'=>trans('textChat.room name'),'required'=>'required']])

        <div class="form-group">
            <div class="form-layout-footer">
                <button type="submit" class="btn btn-success" > {{ trans('textChat.add user') }}</button>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@endpush
@section('content')
    <div class="row">
        @if(isset($rooms))
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('textChat.Name') }}</th>
                    <th class="text-center"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($rooms as $room)
                    <tr class="text-center">
                        <td>{{ $room }}</td>

                        <td>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2 form-group">
                                    <a class="btn btn-xs btn-success" href="{{  route('admin.textChat.roomMessages',$room) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('app.Logs') }}">
                                        {{trans('textChat.List Messages')}}
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
