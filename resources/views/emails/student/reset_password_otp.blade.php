@extends('mail_layout.student.master')

@section('content')
    {{ trans('emails.Forget Password Title') }}<br>
    {{ trans('app.forget password otp' , ['otp'=> $code]) }}<br>
    {{ trans('emails.Thanks') }},<br>
    {{ config('app.name') }}
@endsection
