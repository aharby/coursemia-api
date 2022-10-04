@php
    $attributes=['class'=>'form-control','label'=>trans('users.First name'),'placeholder'=>trans('users.First name'),'required'=>1];
@endphp

@include('form.input',['type'=>'text','name'=>'first_name','attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('users.Last name'),'placeholder'=>trans('users.Last name'),'required'=>1];
@endphp

@include('form.input',['type'=>'text','name'=>'last_name','attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('users.Language'),'required'=>1];
@endphp
@include('form.select',['name'=>'language','options'=>$languages, $row->language ?? null ,
'attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('users.Email'),'placeholder'=>trans('users.Email'),'required'=>1];
@endphp

@include('form.input',['type'=>'text','name'=>'email','attributes'=>$attributes])
@php
    $attributes=['class'=>'form-control','label'=>trans('users.Profile Picture')];
@endphp
@include('form.file',['name'=>'profile_picture','value'=>$row->profile_picture ?? null,'attributes'=>$attributes])


@php
    $attributes=['class'=>'form-control','label'=>trans('users.Old Password'),'placeholder'=>trans('users.Old Password')];
@endphp
@include('form.password',['name'=>'old_password','attributes'=>$attributes])


@php
    $attributes=['class'=>'form-control','label'=>trans('users.Password'),'placeholder'=>trans('users.Password')];
@endphp
@include('form.password',['name'=>'password','attributes'=>$attributes])


@php
    $attributes=['class'=>'form-control','label'=>trans('users.Password confirmation'),'placeholder'=>trans('users.Password confirmation')];
@endphp
@include('form.password',['name'=>'password_confirmation','attributes'=>$attributes])

