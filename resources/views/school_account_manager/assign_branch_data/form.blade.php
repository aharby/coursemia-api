
{!! Form::hidden('branch_id',$branch->id)!!}
{!! Form::hidden('educational_system_id',$educationalSystem->id)!!}

@include('form.input',['type'=>'text','name'=>'branch_name','value'=> $branch->name ?? null,
'attributes'=>['class'=>'form-control', 'disabled', 'label'=>trans('school-account-branches.branch'),'placeholder'=>trans('school-account-branches.name'),'required'=>'required']])

@include('form.input',['type'=>'text','name'=>'educational_system','value'=> $educationalSystem->name ?? null,
'attributes'=>['class'=>'form-control', 'disabled', 'label'=>trans('school-account-branches.educational system'),'placeholder'=>trans('school-account-branches.name'),'required'=>'required']])

@include('form.multiselect',['name'=>'grade_classes[]','options'=> $gradeClasses , 'value'=> $selectedGradeClasses ?? [] ,
'attributes'=>['id'=>'grade_class_id','class'=>'form-control select2', $selectedGradeClasses ? 'disabled' : '' ,'label'=>trans('school-account-branches.Grade Classes'),'placeholder'=>trans('school-account-branches.Grade Classes')]])

@include('form.select',['name'=>'academic_year_id','options'=> $academicYears , 'value'=> $branchEducationalSystem->academic_year_id ?? null ,
'attributes'=>['id'=>'grade_class_id','class'=>'form-control',$branchEducationalSystem->academic_year_id ? 'disabled' : '','label'=>trans('school-account-branches.Academic Year'),'placeholder'=>trans('school-account-branches.Academic Year')]])

@include('form.select',['name'=>'educational_term_id','options'=> $educationalTerms , 'value'=> $branchEducationalSystem->educational_term_id ?? null ,
'attributes'=>['id'=>'educational_term_id','class'=>'form-control',$branchEducationalSystem->educational_term_id ? 'disabled' : '','label'=>trans('school-account-branches.Educational Term'),'placeholder'=>trans('school-account-branches.Educational Term')]])

