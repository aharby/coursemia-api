    @include('form.multiselect',[
        'name'=> 'instructedStudents[]',
        'error_name'=> 'instructed_students',
        'options'=> $students,
        'attributes'=> [
            'id'=>'users',
            'class'=>'form-control select2',
            'label'=>trans('users.Instructed students'),
            'stared'  => true,
            ]
        ]
    )
