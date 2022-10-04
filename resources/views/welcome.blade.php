@extends('layouts.welcome')

@section('content')
    <div class="title m-b-md">
        {{ AppName() }}
    </div>
    <div class="m-b-md title">
        Coming Soon
    </div>
<!--  {{ env('SERVER','env var empty') }}  -->
<!--  deploy test  -->
@endsection
