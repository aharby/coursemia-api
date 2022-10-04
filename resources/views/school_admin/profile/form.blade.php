
@include('form.input',['type'=>'text','name'=>'first_name','value'=> old("first_name", $user->first_name ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('users.First name'),'placeholder'=>trans('users.First name'),'required'=>'required']])


@include('form.input',['type'=>'text','name'=>'last_name','value'=> old("last_name", $user->last_name ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('users.Last name'),'placeholder'=>trans('users.Last name'),'required'=>'required']])


@include('form.input',['type'=>'text','name'=>'username','value'=> old("username", $user->username ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('students.username'),'placeholder'=>trans('students.username'),'required'=>'required']])

@include('form.input',['type'=>'email','name'=>'email','value'=> old("email", $user->email ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('students.Email'),'placeholder'=>trans('students.Email')]])

@include('form.input',['type'=>'text','name'=>'mobile','value'=> old("mobile", $user->mobile ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('users.Mobile'),'placeholder'=>trans('users.Mobile')]])
