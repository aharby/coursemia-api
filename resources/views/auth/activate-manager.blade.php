@extends('layouts.auth_school_layout')

@section('title',trans('login.Activate School Account'))

@section('content')
    <h4>{{ trans('login.Activate School Account') }}</h4>
        {{ Form::open(['route'=>['auth.post.activate-manager',$confirm_token]]) }}
    <div class="form-group">
        <input id="first_name" type="text" class="form-control" name="first_name" value="{{ old("first_name", $user->first_name?? null) }}"
               placeholder="{{ trans('users.First name') }}" required autofocus>
    </div>

    <div class="form-group">
        <input id="last_name" type="text" class="form-control" name="last_name" value="{{ old("last_name", $user->last_name?? null) }}"
               placeholder="{{ trans('users.Last name') }}" required autofocus>
    </div>

    <div class="form-group">
        <input id="email" type="email" class="form-control" name="email" value="{{ old("email", $user->email?? null) }}"
               placeholder="{{ trans('users.email') }}" required>
    </div>

    <div class="form-group">
        <input id="password" type="password" class="form-control" name="password"
               placeholder="{{ trans('users.password') }}" required>
    </div>

    <div class="form-group">
        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation"
               placeholder="{{ trans('users.Password confirmation') }}" required>
    </div>


    @if (!$errors->isEmpty())
        <div class="alert alert-danger" role="alert">
            {!! $errors->first() !!}
        </div>
    @endif

    <div>
        <button class="btn btn-dark submit" type="submit">{{ trans('app.Activate') }}</button>

    </div>
    {{ Form::close() }}
@endsection

