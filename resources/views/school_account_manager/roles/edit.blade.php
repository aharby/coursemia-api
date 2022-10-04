@extends('layouts.school_manager_layout')
@section('title', @$page_title)
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        {!! Form::model( ['method' => 'post']) !!}
                        @include('school_account_manager.roles._form')
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
