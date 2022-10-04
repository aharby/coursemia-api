<div class="row mg-t-20 form-group">
{{--    <div class="form-group">--}}
        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name" >
            {{ @$attributes['label'] }}
            <span class="{{ (@$attributes['required'])?'required':'' }} red">{{ (@$attributes['required'])?'*':'' }} {{ (@$attributes['stared'])?'*':'' }}</span>
        </label>
        @php
            if(isset($attributes['required'])){
                unset($attributes['required']);
            }
        @endphp

        <div class="col-md-6 col-sm-6 col-xs-12">
            {!! Form::password($name,$attributes)!!}
            @if(@$errors)
                @php
                    $name=(isset($error_name))?$error_name:$name;
                @endphp
                <ul class="parsley-errors-list filled">
                    @foreach(@$errors->get($name) as $message)
                        <li class="parsley-required">{{ @$message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
{{--    </div>--}}
</div>
