
@include('form.input',['type'=>'text','name'=>'school_account_name','value'=> $row->schoolAccount->name ?? null,
'attributes'=>['class'=>'form-control', 'disabled', 'label'=>trans('school-account-branches.school account name'),'placeholder'=>trans('school-account-branches.name'),'required'=>'required']])

@include('form.input',['type'=>'text','name'=>'name','value'=> $row->name ?? null,
'attributes'=>['class'=>'form-control','label'=>trans('school-account-branches.branch name'),'placeholder'=>trans('school-account-branches.name'),'required'=>'required']])

@include('form.input',['type'=>'text','name'=>'supervisor_id','value'=> $row->supervisor ? $row->supervisor->username : null,
'attributes'=>['class'=>'form-control',$row->supervisor ? 'disabled' : '','label'=>trans('school-account-branches.supervisor id'),'placeholder'=>trans('school-account-branches.supervisor id'),'required'=>'required']])

@include('form.input',['type'=>'text','name'=>'leader_id','value'=> $row->leader ? $row->leader->username : null,
'attributes'=>['class'=>'form-control',$row->leader ? 'disabled' : '','label'=>trans('school-account-branches.leader id'),'placeholder'=>trans('school-account-branches.leader id'),'required'=>'required']])


@include('form.input',['name'=>'country','type' => 'text', 'value' => \App\OurEdu\Countries\Country::find($row->schoolAccount->country_id)->name ?? null ,
'attributes'=>['id'=>'country_id','class'=>'form-control','disabled','required'=>'required','label'=>trans('school-account-branches.Country'),'placeholder'=>trans('subjects.Country')]])

@include('form.multiselect',['name'=>'educational_systems[]','options'=> $educationalSystems , 'value'=> $row->educationalSystems?? [] ,
'attributes'=>['id'=>'educational_system_id','class'=>'form-control select2' ,$row->educationalSystems()->exists() ? 'disabled':'','label'=>trans('school-account-branches.Educational Systems'),'placeholder'=>trans('school-account-branches.Educational System')]])

