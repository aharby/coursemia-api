@extends('layouts.auth_school_layout')

@section('title',trans('app.Login'))

@section('content')
    @include('flash::message')

    <h4>{{ trans('auth.Welcome back') }}</h4>
    <h6 class="font-weight-light">{{ trans('app.Login') }}</h6>
    <form method="POST" action="{{ route('auth.post.login', \App\OurEdu\Users\UserEnums::SCHOOL_SUPERVISOR) }}" class="pt-3">
        @csrf
        <div class="form-group">
            <input id="username" type="text" class="form-control form-control-lg{{ $errors->has('username') ? ' is-invalid' : '' }}" name="email" value="{{ old('username') }}" placeholder="{{ trans('auth.Enter your email') }}" required autofocus>
            @if($errors->has('username'))
                <span class="invalid-feedback" role="alert">
                                        {{ $errors->first('username') }}
                                    </span>
            @endif
        </div>
        <div class="form-group">
            <input id="password" type="password" class="form-control form-control-lg{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{ trans('auth.Enter your password') }}">

            @if ($errors->has('password'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>
        <div class="mt-3">
            <input type="hidden" name="guard" value="{{Request::get('guard')}}">
            <input type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn" value="{{ trans('app.Login') }}">
        </div>
    </form>
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if (!$errors->isEmpty())
        <div class="alert alert-danger" role="alert">
            {!! $errors->first() !!}
        </div>
    @endif
@endsection

