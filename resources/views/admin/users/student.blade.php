<div class="type type-student" style="display: none">
{{--    @include('form.select',['name'=>'country_id','options'=> $countries , $row->country_id ?? null ,--}}
{{--    'attributes'=>['id'=>'country_id','class'=>'form-control','label'=>trans('educational_systems.Country'),'placeholder'=>trans('subjects.Country')]])--}}

    @include('form.select',['name'=>'educational_system_id','options'=> $educationalSystems , 'value'=> $relation->educational_system_id ?? null ,
    'attributes'=>['id'=>'educational_system_id','class'=>'form-control educational','label'=>trans('subjects.Educational System'),'placeholder'=>trans('subjects.Educational System')]])

    @include('form.select',['name'=>'class_id','options'=> $gradeClasses , 'value'=> $relation->class_id ?? null ,
    'attributes'=>['id'=>'class_id','class'=>'form-control ','label'=>trans('subjects.Grade Class'),'placeholder'=>trans('subjects.Grade Class')]])

    @include('form.select',['name'=>'academical_year_id','options'=> $academicalYears , 'value'=> $relation->academical_year_id ?? null ,
    'attributes'=>['id'=>'academical_year_id','class'=>'form-control ','label'=>trans('subjects.Academic year'),'placeholder'=>trans('subjects.Academic year')]])

    @include('form.select',['name'=>'school_id','options'=> $schools ,'value' => $relation->school_id ?? null ,'attributes'=>['id'=>'school','class'=>'form-control','label'=>trans('users.School'),'placeholder'=>trans('users.School')]])

    @php
        $attributes=['class'=>'form-control datepicker' ,'onkeydown'=>'return false' ,'label'=>trans('users.Birth Date'),'placeholder'=>trans('users.Birth Date')];
    @endphp
    @include('form.input',['name'=>'birth_date','type'=>'text', 'value' => $relation->birth_date ?? null,'attributes'=>$attributes])

</div>


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
    </script>
    <script>
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
                        let options = '<option selected="selected" value=""> {{trans('subjects.Grade Class')}} </option>';
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
    </script>

@endpush

