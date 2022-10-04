@extends('layouts.welcome')
@extends('layouts.app')

@section('content')
    <div class="title m-b-md">
        {{ AppName() }}
    </div>
    <div class="m-b-md title">
        <h9 class="">{{trans('app.You do not have authorized  to view this page')}}</h9><br>
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
