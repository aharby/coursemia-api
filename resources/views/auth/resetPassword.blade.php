@extends('layouts.admin_auth_layout')

@section('body_class','login')

@section('content')
    <div>
        <div class="login_wrapper">
            <div class="animate form login_form">
                @include('flash::message')
                <section class="login_content">
                    {{ Form::open(['route'=>'auth.post.resetPassword']) }}
                    <h1>{{ trans('auth.forget password') }}</h1>
                    <div>
                        <input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}"
                               placeholder="{{ trans('app.email') }}" required autofocus>
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
                        <button class="btn btn-dark submit"  type="submit">{{ trans('app.Send mail') }}</button>

                    </div>

                    <div class="clearfix"></div>

                    <div class="separator">

                        <div class="clearfix"></div>
                        <br/>

                        <div>
                            <div class="h1">{{ appName() }}</div>
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
