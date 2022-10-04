<div class="title_right">
    <div class="col-md-8 col-sm-8 col-xs-12 form-group pull-right top_search">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">{{ trans('app.Home') }}</a></li>
            @if(@$breadcrumb)
                @foreach ($breadcrumb as $key=>$value)
                    <li><a href="{{ $value }}">{{ $key }}</a></li>
                @endforeach
            @endif
            <li class="active" aria-current="page">{{ @$page_title }}</li>
        </ol>
    </div>
</div>