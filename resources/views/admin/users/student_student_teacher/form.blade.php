@include('form.select', ['name'=>'subject_id','options'=> $subjects,
                'attributes'=>['class'=>'form-control','required'=>'required',
                    'label'=>trans('users.Subjects'),'placeholder'=>trans('users.Subjects')]])

@include('form.select', ['name'=>'student_teacher_id','options'=> $student_teachers,
                'attributes'=>['class'=>'form-control','required'=>'required',
                    'label'=>trans('users.Student Teachers'),'placeholder'=>trans('users.Student Teachers')]])

