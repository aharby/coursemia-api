@extends('layouts.admin_auth_layout')

@section('body_class','login')

@section('content')
    <div>
        <div class="login_wrapper">
            <div class="animate form login_form">
                @include('flash::message')
                <section class="login_content">
                    {{ Form::open(['route'=>['auth.post.updatePassword',$token]]) }}
                    <h1>{{ trans('auth.change password') }}</h1>

                    <div>
                        <input id="password" type="password" class="form-control" name="password"
                               placeholder="{{ trans('app.password') }}" required>
                    </div>

                    <div>
                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation"
                               placeholder="{{ trans('app.password confirmation') }}" required>
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
                        <button class="btn btn-dark submit" type="submit">{{ trans('app.change password') }}</button>


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
