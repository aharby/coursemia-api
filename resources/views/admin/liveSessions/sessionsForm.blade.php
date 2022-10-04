@php
    $disabled = false;
         if($row){
        if (\Carbon\Carbon::parse($row->date . ' ' . $row->start_time)->lessThanOrEqualTo(\Carbon\Carbon::now())) {
        $disabled = true;
        }
        }
@endphp
        @include('form.input',['type'=>'text','name'=>"content",
        'attributes'=>['class'=>'form-control','label'=>trans('live_sessions.Session content'),'placeholder'=>trans('live_sessions.Session content'),'required'=>'required']])

        @include('form.input',['type'=>'text','name'=>"date",
        'attributes'=>['class'=>'form-control nowdatepicker','label'=>trans('live_sessions.Session date'),'placeholder'=>trans('live_sessions.Session date'),'required'=>'required','disabled'=> $disabled]])

        @include('form.input',['type'=>'text','name'=>'start_time',
        'attributes'=>['class'=>'form-control timepicker','label'=>trans('live_sessions.Session start time'),'placeholder'=>trans('live_sessions.Session start time'),'required'=>'required','disabled'=> $disabled]])

        @include('form.input',['type'=>'text','name'=>'end_time',
        'attributes'=>['class'=>'form-control timepicker','label'=>trans('live_sessions.Session end time'),'placeholder'=>trans('live_sessions.Session end time'),'required'=>'required','disabled'=> $disabled]])
