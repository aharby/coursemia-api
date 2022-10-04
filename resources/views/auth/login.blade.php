@extends('layouts.admin_auth_layout')

@section('body_class','login')

@section('content')
    <div>
        <div class="login_wrapper">
            <div class="animate form login_form">
                @include('flash::message')
                <section class="login_content">

                    {{Html::image("img/color-logo.svg" , 'alt text', array('class' => 'login-img'))}}
                    {{ Form::open(['route'=>['auth.post.login', \App\OurEdu\Users\UserEnums::ADMIN_TYPE]]) }}
                    <h1>{{ trans('login.Admin Login') }}</h1>


                    <div>
                        <input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}"
                               placeholder="{{ trans('app.email') }}" required autofocus>
                    </div>
                    <div>
                        <input id="password" type="password" class="form-control" name="password"
                               placeholder="{{ trans('app.password') }}" required>
                    </div>
                    <div class="checkbox al_left">
                        <label>
                            <input type="checkbox"
                                   name="remember" {{ old('remember') ? 'checked' : '' }}> {{ trans('app.Remember Me') }}
                        </label>
                    </div>

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

                    <div>
                        <button class="btn btn-dark submit" type="submit">{{ trans('app.Login') }}</button>

                        <a class="reset_pass" href="{{ route('auth.get.resetPassword') }}">
                            {{ trans('app.reset Password') }}
                        </a>
                    </div>

                    <div class="clearfix"></div>

{{--                    <div class="separator">--}}
{{--                        <div>--}}
{{--                            <a href="#" class="btn btn-success btn-google-plus">--}}
{{--                                <i class="fa fa-google-plus"></i>--}}
{{--                                Google+--}}
{{--                            </a>--}}
{{--                            <a href="#" class="btn btn-success btn-facebook">--}}
{{--                                <i class="fa fa-facebook"></i>--}}
{{--                                Facebook--}}
{{--                            </a>--}}
{{--                            <a href="#" class="btn btn-success btn-twitter">--}}
{{--                                <i class="fa fa-twitter"></i>--}}
{{--                                Twitter--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <div class="separator">
{{--                        <p class="change_link">{{ trans('views.auth.login.message_1') }}--}}
{{--                            <a href="#" class="to_register"> {{ trans('views.auth.login.action_2') }} </a>--}}
{{--                        </p>--}}

{{--                        <div class="clearfix"></div>--}}

                        <div>
                            <div class="h1"></div>
                            <p>&copy; {{ date('Y') }} {{ appName() }}
                                . {{ trans('app.Copyright') }}</p>
                        </div>
                    </div>
                    {{ Form::close() }}
                </section>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    @parent

    {{ Html::style(mix('assets/auth/css/login.css')) }}
@endsection
