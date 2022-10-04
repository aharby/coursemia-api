@extends('layouts.welcome')
@extends('layouts.app')

@section('content')
    <div class="title m-b-md">
        {{ AppName() }}
    </div>
    <div class="m-b-md title"><br>
        <h9 class="error-title">404</h9><br>
        <h8 class="tx-sm-24 tx-normal">{!!trans('app.Oopps This page Not Found !')!!}</h8><br>
        <button type="button" class="btn btn-primary btn-lg"><a style="color: black" href="{{route('welcome.index')}}">{{trans('app.Back To Home')}}</a></button>

    </div>
@endsection

@section('styles')
    @if(app()->getLocale() == 'ar')
        {{ Html::style(mix('assets/admin-rtl/css/admin.css')) }}
    @else
        {{ Html::style(mix('assets/admin/css/admin.css')) }}
    @endif
    @stack('css')

@endsection
