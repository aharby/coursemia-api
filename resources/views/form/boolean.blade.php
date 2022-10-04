<div class="row mg-t-20">
    <label class="control-label col-md-2 col-sm-2 col-xs-12">{{ @$attributes['label'] }} <span class="tx-danger red">{{ (@$attributes['required'])?'*':'' }}</span></label>
    <div class="col-sm-8 mg-t-10 mg-sm-t-0">
        <div class="row mg-t-10">
            <div class="col-lg-4">
                @php
                    $attributes['class'] = '';
                @endphp
                <label class="rdiobox">
                    {!! Form::radio($name,1,null,$attributes) !!}
                    <span>{{trans('app.Yes')}} </span>
                </label>
            </div><!-- col-3 -->
            <div class="col-lg-4">
                <label class="rdiobox">
                    {!! Form::radio($name,0,null,$attributes) !!}
                    <span>{{trans('app.No')}} </span>
                </label>
            </div><!-- col-3 -->


            @if(@$errors)

                @php
                    $error_name =(isset($error_name))?$error_name:$name;
                @endphp

                @foreach($errors->get($error_name) as $message)
                    <span class='help-inline text-danger'>{{ $message }}</span>
                @endforeach

            @endif
        </div>
    </div>
</div>



