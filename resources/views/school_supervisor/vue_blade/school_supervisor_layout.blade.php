@extends('layouts.school_manager_layout')
@section('title', @$page_title)



@section('content')
        <div id="app">
            <app></app>
        </div>
    <script src="{{ mix('js/app-vue.js') }}" ></script>
@endsection
