
@include('form.input',['type'=>'text','name'=>'first_name','value'=> old("first_name", $educationalSupervisor->first_name ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('educationalSupervisor.First name'),'placeholder'=>trans('educationalSupervisor.First name'),'required'=>'required']])


@include('form.input',['type'=>'text','name'=>'last_name','value'=> old("last_name", $educationalSupervisor->last_name ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('educationalSupervisor.Last name'),'placeholder'=>trans('educationalSupervisor.Last name'),'required'=>'required']])


@include('form.input',['type'=>'text','name'=>'username','value'=> old("username", $educationalSupervisor->username ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('educationalSupervisor.ID'),'placeholder'=>trans('educationalSupervisor.ID'),'required'=>'required']])

@include('form.input',['type'=>'email','name'=>'email','value'=> old("email", $educationalSupervisor->email ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('educationalSupervisor.Email'),'placeholder'=>trans('educationalSupervisor.Email')]])

@include('form.password',['name'=>'password',
'attributes'=>['class'=>'form-control', 'label'=>trans('educationalSupervisor.password'),'placeholder'=>trans('educationalSupervisor.password')]])

@include('form.input',['type'=>'text','name'=>'mobile','value'=> old("mobile", $educationalSupervisor->mobile ?? null),
'attributes'=>['class'=>'form-control', 'label'=>trans('educationalSupervisor.mobile'),'placeholder'=>trans('educationalSupervisor.mobile')]])


@include('form.select',[
    'name'=>'educational_system_id',
    'options'=> $educationalSystems,
    'value'=> $selectedEducationalSystem?? 'null',
    'attributes' => [
        'id'=>'educational_system_id',
        'class'=>'form-control',
        'label'=>trans('educationalSupervisor.educational system'),
        'placeholder'=>trans('educationalSupervisor.educational system')
    ]
])

@include('form.multiselect',['name'=>'gradeClasses[]','error_name'=>'gradeClasses','options'=> $gradeClasses?? [] , 'value'=> $selectedGradeClasses ?? [] ,
'attributes'=>['id'=>'grade_class_id','class'=>'form-control select2','label'=>trans('educationalSupervisor.grade class')]])

@include('form.multiselect',['name'=>'subjects[]','error_name'=>'subjects','options'=> [] , 'value'=> $selectedSubjects ?? [] ,
'attributes'=>['id'=>'subject_id','class'=>'form-control select2','label'=>trans('educationalSupervisor.subjects')]])
<input type="hidden" value="{{ $branch->id ?? null }}" name="branch_id">



@push('scripts')
    <script type="text/javascript">
    $(function() {
        let subjects = <?php echo json_encode($subjects)?>;
        let selectedSubjects =<?php echo json_encode($selectedSubjects)?>;
        $.each(subjects, function (i, item) {
            if(selectedSubjects.includes(item.id)){
                $('#subject_id').append(`<option selected value="${item.id}-${item.grade_class.id}">${item.name} - ${item.grade_class.title} </option>`);
            }else{
                $('#subject_id').append(`<option value="${item.id}-${item.grade_class.id}">${item.name} - ${item.grade_class.title} </option>`);
            }
        });

        $("#educational_system_id").change(function () {
            $('#grade_class_id').empty();
            $('#subject_id').empty();
            let educational_system_id = $(this).val();
            $.get('{{ route('school-branch-supervisor.educational-supervisors.getGradeClassesByEducationalSystem') }}' + `/${educational_system_id}`,
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'educationalSystemId': $(this).val(),
                })
                .done(function (response) {

                    $.each(response.gradeClasses, function (i, item) {
                        var option = new Option(item.title, item.id, false, false);

                        $('#grade_class_id').append(option);
                    });
                });
        });


        $("#grade_class_id").change(function () {
            $('#subject_id').empty();
            let grade_class_id = $(this).val();
            $.get('{{ route('school-branch-supervisor.educational-supervisors.getSubjectsByGradeClass') }}' + `/${grade_class_id}`,
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'gradeClassID': $(this).val(),
                })
                .done(function (response) {
                    $.each(response.subjects, function (i, item) {

                        var option = new Option(item.name + " - " + item.grade_class.title, item.id + "-" + item.grade_class.id)

                        $('#subject_id').append(option);
                    });
                });
        });





        @if(request()->old('educational_system_id'))
        $('#educational_system_id').trigger('change');
        @endif
        @if(request()->old('branch_id'))
            $('#branch_id').trigger('change');
        @endif
        @if(request()->old('grade_class_id'))
        $('#grade_class_id').trigger('change');
        @endif



            $('#school_account_user_type').change(function(e) {
                var type = $(this).val();
                $("#parent_school_account_roles").hide();

            });
        });
    </script>
@endpush

