
@include('form.input',['type'=>'text','name'=>'first_name','value'=> old("first_name", $parent->first_name ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('users.First name'),'placeholder'=>trans('users.First name'),'required'=>'required']])


@include('form.input',['type'=>'text','name'=>'last_name','value'=> old("last_name", $parent->last_name ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('users.Last name'),'placeholder'=>trans('users.Last name'),'required'=>'required']])


@include('form.input',['type'=>'text','name'=>'username','value'=> old("username", $parent->username ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('users.id'),'placeholder'=>trans('users.id'),'required'=>'required']])

@include('form.input',['type'=>'email','name'=>'email','value'=> old("email", $parent->email ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('students.Email'),'placeholder'=>trans('students.Email')]])

@include('form.input',['type'=>'text','name'=>'mobile','value'=> old("mobile", $parent->mobile ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('users.Mobile'),'placeholder'=>trans('users.Mobile')]])
