<style>
hr {
display: block;
margin-top: 0.5em;
margin-bottom: 0.5em;
margin-left: auto;
margin-right: auto;
border-style: inset;
border-width: 2px;
}
</style>
@foreach(config("translatable.locales") as $lang)
    @php
        $attributes=['class'=>'form-control','label'=>trans('roles.Title').' '.$lang,'placeholder'=>trans('roles.Title').' '.$lang,'required'=>1];
    @endphp
    @include('form.input',['type'=>'text','name'=>'title:'.$lang,'value'=> $row->title[$lang] ?? null,'attributes'=>$attributes])
@endforeach

@if(!isset($row->title))
    @include('form.select',['name'=>'type','options'=>$optionTypes,'attributes'=>['class'=>'form-control select2','label'=>trans('options.Type'),'placeholder'=>trans('options.Select type'), 'required'=> 1]])
@endif
@if(config('permession-modules'))
    @foreach(config('permession-modules') as $key=>$permissions)
        <h5 class="mg-b-10 mg-t-20">
            <label class="ckbox">
                {!! Form::checkbox('parents',NULL,isset($row->permissions) ? in_array($permissions,$row->permissions) ? true : false : false,['id'=>$key,'class'=>'parents']) !!}
                <span><b>{{ucfirst($key)}}</b></span>
            </label>
        </h5>
        <div class="row">
            @foreach ($permissions as $permission)
                <div class="col-lg-3">
                    <label class="ckbox">
                        {!! Form::checkbox('permissions[]',$permission.'-'.$key,isset($row->permissions) ? in_array($permission.'-'.$key,$row->permissions) ? true : false : false,['id'=>$permission.'-'.$key,'class'=>'childs childs_'.$key,'for'=>$key]) !!}
                        <span>{{ucfirst($permission)}} {{ucfirst($key)}}</span>
                    </label>
                </div>
            @endforeach
        </div>
        <hr >
    @endforeach
@endif
@if(@$errors)
    @foreach($errors->get('permissions') as $message)
        <span class='help-inline text-danger'>{{trans('roles.Choose at least 1 permission')}}</span>
    @endforeach
@endif


<div class="form-group">
    <button type="submit" class="btn btn-gradient-success btn-sm">
        <i class="mdi mdi-content-save"></i> {{trans('app.Save')}}
    </button>
</div>

@push('scripts')
    <script>
        $('.parents').on('change', function () {
            if ($(this).is(':checked')) {
                $('.childs_' + $(this).attr('id')).prop('checked', true);
            } else {
                $('.childs_' + $(this).attr('id')).prop('checked', false);
            }
        });
        $('.childs').on('change', function () {
            var parent = $(this).attr("for");
            if ($(this).is(':checked')) {
                $('#' + parent).prop('checked', true);
            } else {
                if ($('.childs_' + parent + ":checked").size() == 0) {
                    $('#' + parent).prop('checked', false);
                }
            }
        });
        $('.childs').trigger('change');
    </script>
@endpush
