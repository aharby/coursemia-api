@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush

@section('title',@$page_title)

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 x_panel">

            {{ Form::open(['method' => 'post','class'=>'form-vertical form-label-left', 'files' => true ]) }}
            @include('admin.psychological_questions.form')
            <div class="form-group">
                <div class="form-layout-footer">
                    <button type="submit" class="btn btn-success" > {{ trans('app.Save') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
@push('js')

<script>

</script>

