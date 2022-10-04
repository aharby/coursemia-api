<br>
<div class="type type-content_author" style="display: none">
    @php
        $attributes=['class'=>'form-control datepicker' ,'onkeydown'=>'return false' ,'label'=>trans('users.Hire Date'),'placeholder'=>trans('users.Hire Date')];
    @endphp
    @include('form.input',['name'=>'hire_date','type'=>'text', 'value' => $relation->hire_date ?? null,'attributes'=>$attributes])
</div>