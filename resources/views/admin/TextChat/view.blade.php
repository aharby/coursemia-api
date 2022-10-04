@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <h3>{{ trans('textChat.Join User To Room') }}</h3>
        {{ Form::open(['route'=>'admin.textChat.joinRoom','method' => 'post','class'=>'form-vertical form-label-left' , 'files' => false  ]) }}
        @include('form.input',['type'=>'hidden','name'=>'room', 'value' => $room ,'attributes'=> []])
         @include('form.input',['type'=>'text','name'=>'userName',
        'attributes'=>['class'=>'form-control','label'=>trans('textChat.userName'),'placeholder'=>trans('textChat.userName'),'required'=>'required']])
        @include('form.input',['type'=>'text','name'=>'socketID',
        'attributes'=>['class'=>'form-control','label'=>trans('textChat.socketID'),'placeholder'=>trans('textChat.socketID'),'required'=>'required']])

        <div class="form-group">
            <div class="form-layout-footer">
                <button type="submit" class="btn btn-success" > {{ trans('textChat.join user to room') }}</button>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@endpush
@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>
            @if(isset($messages))
                @foreach($messages as $message)
                    <tr>
                        <th width="10%" class="text-center">{{$message['id']}}</th>
                        <th width="20%" class="text-center">{{$message['author']}}</th>
                        <th width="20%" class="text-center">{{date( 'Y-m-d H:i:s', $message['timestamp'])}}</th>
                        <td width="50%" class="text-center">{{$message['textMessage']}}</td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
@endsection
