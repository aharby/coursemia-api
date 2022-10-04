@include('form.select',['name'=>'school_account_id','options'=> $schoolAccounts , 'value'=> $selectedSchoolAccounts ?? null ,
'attributes'=>['id'=>'school_account_id','class'=>'form-control select','label'=>trans('school-account-branches.School Account'),'placeholder'=>trans('school-account-branches.School Account')]])

@include('form.select',['name'=>'meeting_type','options'=> $meetingTypes , $row->meeting_type ?? null ,
'attributes'=>['id'=>'meeting_type','class'=>'form-control','label'=>trans('school-accounts.meeting type'),'placeholder'=>trans('school-accounts.meeting type'),]])

@include('form.input',['type'=>'text','name'=>'name','value'=> $row->name ?? null,
'attributes'=>['class'=>'form-control','label'=>trans('school-account-branches.branch name'),'placeholder'=>trans('school-account-branches.name'),'required'=>'required']])

@php
    $attributes=['class'=>'form-control','label'=>trans('users.active sms'),'required'=>1];
@endphp
@include('form.boolean',['name'=>'sms','value'=>$row->sms ?? null,'attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('users.Is active'),'required'=>1];
@endphp
@include('form.boolean',['name'=>'is_active','value'=>$row->is_active ?? null,'attributes'=>$attributes])

