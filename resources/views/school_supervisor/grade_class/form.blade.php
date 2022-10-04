
@php
    $attributes=['class'=>'form-control','label'=>trans('users.First name'),'placeholder'=>trans('users.First name'),'required'=>1];
@endphp
@include('form.input',['type'=>'text','name'=>'first_name','value'=> $row->first_name ?? null,'attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('users.Last name'),'placeholder'=>trans('users.Last name'),'required'=>1];
@endphp
@include('form.input',['type'=>'text','name'=>'last_name','value'=> $row->last_name ?? null,'attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('users.Email'),'placeholder'=>trans('users.Email')];
@endphp
@include('form.input',['type'=>'text','name'=>'email','value'=> $row->email ?? null,'attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('users.id'),'placeholder'=>trans('users.id'),'required'=>1];
@endphp
@include('form.input',['type'=>'text','name'=>'username','value'=> $row->username ?? null,'attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('users.Mobile'),'placeholder'=>trans('users.Mobile')];
@endphp
@include('form.input',['type'=>'text','name'=>'mobile','value'=> $row->mobile ?? null,'attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('users.Password'),'placeholder'=>trans('users.Password')];
@endphp
@include('form.password',['name'=>'password','attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('users.Password confirmation'),'placeholder'=>trans('users.Password confirmation')];
@endphp
@include('form.password',['name'=>'password_confirmation','attributes'=>$attributes])
