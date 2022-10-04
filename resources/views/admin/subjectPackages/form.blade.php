@include('form.input',['type'=>'text','name'=>'name','value'=> $row->name ?? null,
'attributes'=>['class'=>'form-control','label'=>trans('subject_packages.name'),'placeholder'=>trans('subject_packages.name'),'required'=>'required']])

@php
    $attributes=['id'=>'picture','class'=>'form-control','label'=>trans('subject_packages.Picture'),'placeholder'=>trans('subject_packages.Picture'), 'required' => isset($row->id) ? false : true];
@endphp
@include('form.file',['name'=>'picture','value' => $row->picture ?? null, 'attributes'=> $attributes])

@include('form.select',['name'=>'country_id','options'=> $countries , $row->country_id ?? null ,
'attributes'=>['id'=>'country_id','class'=>'form-control','required'=>'required','label'=>trans('educationalSystems.Country'),'placeholder'=>trans('subject_packages.Country')]])

@include('form.select',['name'=>'educational_system_id','options'=> $educationalSystems , 'value'=> $row->educational_system_id ?? null ,
'attributes'=>['id'=>'educational_system_id','class'=>'form-control educational','required'=>'required','label'=>trans('subject_packages.Educational System'),'placeholder'=>trans('subject_packages.Educational System')]])

@include('form.select',['name'=>'grade_class_id','options'=> $gradeClasses , 'value'=> $row->grade_class_id ?? null ,
'attributes'=>['id'=>'grade_class_id','class'=>'form-control ','required'=>'required','label'=>trans('subject_packages.Grade Class'),'placeholder'=>trans('subject_packages.Grade Class')]])

@include('form.select',['name'=>'academical_years_id','options'=> $academicalYears , 'value'=> $row->academical_years_id ?? null ,
'attributes'=>['id'=>'academical_years_id','class'=>'form-control ','label'=>trans('subject_packages.Academic year'),'placeholder'=>trans('subject_packages.Academic year')]])

@include('form.input',['type'=>'text','name'=>'price','value'=> $row->price ?? null,
'attributes'=>['class'=>'form-control','label'=>trans('subject_packages.Price'),'placeholder'=>trans('subject_packages.Price'),'required'=>'required']])

@include('form.multiselect',['name'=>'subjects[]','options'=> $subjects , 'value'=> $selectedSubjects ??[] ,
'attributes'=>['id'=>'subject_id','class'=>'form-control select2','label'=>trans('subject_packages.Subjects') ,'required'=>1]])

@include('form.input',['type'=>'textarea','name'=>'description','value'=> $row->description ?? null,
'attributes'=>['class'=>'form-control','label'=>trans('subject_packages.Description'),'placeholder'=>trans('subject_packages.Description'),'required'=>'required']])

@php
    $attributes=['class'=>'form-control','label'=>trans('subject_packages.Is active'),'required'=>1];
@endphp
@include('form.boolean',['name'=>'is_active',$attributes])

@push('js')
    <script>
        {{-- get Educational System by country --}}
        $(document).ready(function () {
            $('#country_id').change(function () {
                $.get('{{ route('admin.educationalSystems.get.educational.system') }}',
                    {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        country_id: this.value,
                    })
                    .done(function(response){
                        @if(!isset($row->educational_system_id))
                        $('#educational_system_id option').remove();
                            @endif
                        let options = '<option selected="selected" value=""> {{trans('app.Educational System')}} </option>';
                        for (let d in response.educationSystem) {
                            options += buildSelectOption(d, response.educationSystem[d], '') + '\n';
                        }
                        $('#educational_system_id').html(options);
                    });

                function buildSelectOption(key, value, selected) {
                    return "<option value='" + key + "'" + selected + ">" + value + "</option>";
                }
            });
        });
        {{-- get grade classes by country --}}
        $(document).ready(function () {
            $('#country_id').change(function () {
                $.get('{{ route('admin.gradeClasses.get.grade.classes') }}',
                    {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        country_id: this.value,
                    })
                    .done(function(response){
                        @if(!isset($row->grade_class_id))
                        $('#grade_class_id option').remove();
                            @endif
                        let options = '<option selected="selected" value=""> {{trans('subject_packages.Grade Class')}} </option>';
                        for (let d in response.gradeClasses) {
                            options += buildSelectOption(d, response.gradeClasses[d], '') + '\n';
                        }
                        $('#grade_class_id').html(options);
                    });

                function buildSelectOption(key, value, selected) {
                    return "<option value='" + key + "'" + selected + ">" + value + "</option>";
                }
            });
        });

        {{-- get subjects by country, educational systems, grade classes or academical years  --}}
        $(document).ready(function () {

            $("#country_id").change(function(){ filter(this) });

            $("#educational_system_id").change(function(){ filter(this) });

            $("#grade_class_id").change(function(){ filter(this) });

            $("#academical_years_id").change(function(){ filter(this) });

            function filter()
            {
                $.get('{{ route('admin.subjects.get.subjects') }}',
                    {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        country_id: $("#country_id").val(),
                        educational_system_id: $("#educational_system_id").val(),
                        grade_class_id: $("#grade_class_id").val(),
                        academical_years_id: $("#academical_years_id").val()
                    })
                    .done(function(response){
                        @if(!isset($row->subject_id))
                        $('#subject_id option').remove();
                            @endif
                        let options;
                        for (let d in response.subjects) {
                            options += buildSelectOption(d, response.subjects[d], '') + '\n';
                        }
                        $('#subject_id').html(options);
                    });

                function buildSelectOption(key, value, selected) {
                    return "<option value='" + key + "'" + selected + ">" + value + "</option>";
                }

            }
        });
    </script>

@endpush




