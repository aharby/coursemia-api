<div class="row mg-t-20 form-group">
    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name" >
        {{ @$attributes['label'] }}
        <span class="{{ (@$attributes['required'])?'required':'' }} red">{{ (@$attributes['required'])?'*':'' }} {{ (@$attributes['stared'])?'*':'' }}</span>
    </label>
    <div class="col-md-6 col-sm-6 col-xs-12">
        @php
            $attributes['autocomplete']='off';
        @endphp
        {!! Form::select($name, $options,(@$row->$name)?:(@$value), $attributes) !!}
        @php
           $name=(isset($error_name))?$error_name:$name;     
        @endphp

        @if(@$errors)
            @php
                $name=(isset($error_name))?$error_name:$name;
            @endphp
            <ul class="parsley-errors-list filled">
                @foreach($errors->get($name) as $message)
                    <li class="parsley-required">{{ $message }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
