
<div class="row mg-t-20 form-group">
    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="old_password">
        {{ trans('users.Old Password') }}
    </label>
    <div class="col-md-6 col-sm-6 col-xs-12">
        <input class="form-control" label="{{ trans('users.Old Password') }}" placeholder="{{ trans('users.Old Password') }}" autocomplete="off" name="old_password" type="password">
        <ul class="parsley-errors-list filled">
        </ul>
    </div>
</div>


<div class="row mg-t-20 form-group">
    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="old_password">
        {{ trans('users.Password') }}
    </label>
    <div class="col-md-6 col-sm-6 col-xs-12">
        <input class="form-control" label="{{ trans('users.Password') }}" placeholder="{{ trans('users.Password') }}" autocomplete="off" name="password" type="password">
        <ul class="parsley-errors-list filled">
        </ul>
    </div>
</div>


<div class="row mg-t-20 form-group">
    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="old_password">
        {{ trans('users.Password confirmation') }}
    </label>
    <div class="col-md-6 col-sm-6 col-xs-12">
        <input class="form-control" label="{{ trans('users.Password confirmation') }}" placeholder="{{ trans('users.Password confirmation') }}" autocomplete="off" name="password_confirmation" type="password">
        <ul class="parsley-errors-list filled">
        </ul>
    </div>
</div>
