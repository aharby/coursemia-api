
@include('form.input',['type'=>'text','name'=>'first_name','value'=> old("first_name", $student->user->first_name ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('users.First name'),'placeholder'=>trans('users.First name'),'required'=>'required']])


@include('form.input',['type'=>'text','name'=>'last_name','value'=> old("last_name", $student->user->last_name ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('users.Last name'),'placeholder'=>trans('users.Last name'),'required'=>'required']])


@include('form.input',['type'=>'text','name'=>'username','value'=> old("username", $student->user->username ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('students.ID'),'placeholder'=>trans('students.ID'),'required'=>'required']])

@include('form.input',['type'=>'email','name'=>'email','value'=> old("email", $student->user->email ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('students.Email'),'placeholder'=>trans('students.Email')]])

@include('form.password',['name'=>'password',
'attributes'=>['class'=>'form-control', 'label'=>trans('students.password'),'placeholder'=>trans('students.password')]])

@include('form.input',['type'=>'text','name'=>'mobile','value'=> old("mobile", $student->user->mobile ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('users.Mobile'),'placeholder'=>trans('users.Mobile')]])

@include('form.select',['name'=>'classroom_id','options'=> $classrooms ,'value' => $student->classroom_id ?? null ,
'attributes'=>['class'=>'form-control', 'required'=>'required','label'=>trans('students.classroom'),'placeholder'=>trans('students.classroom')]])

<input type="hidden" value="{{ $branch->id ?? null }}" name="branch_id">
