<br>
<div class="type type-instructor" style="display: none">
    @include('form.input',['type'=>'textarea','name'=>'about_instructor','value'=> $relation->about_instructor ?? null,
        'attributes'=>['class'=>'form-control','label'=>trans('users.about_instructor'),'placeholder'=>trans('users.about_instructor')]])
    @php
        $attributes=['class'=>'form-control datepicker' ,'onkeydown'=>'return false' ,'label'=>trans('users.Hire Date'),'placeholder'=>trans('users.Hire Date')];
    @endphp
    @include('form.input',['name'=>'hire_date','type'=>'text', 'value' => $relation->hire_date ?? null,'attributes'=>$attributes])

    @include('form.select',['name'=>'school_id','options'=> $schools ,'value' => $relation->school_id ?? null ,'attributes'=>['id'=>'school','class'=>'form-control','label'=>trans('users.School'),'placeholder'=>trans('users.School')]])

</div>
