@include('form.input',['type'=>'text','name'=>'name','value'=> $row->name ?? null,
'attributes'=>['class'=>'form-control','label'=>trans('school-accounts.name'),'placeholder'=>trans('school-accounts.name'),'required'=>'required']])
@php
    $attributes = ['class'=>'form-control','label'=>trans('school-accounts.logo'),'placeholder'=>trans('school-accounts.name'),'accept'=>'image/*'];
    if (!isset($row->id)){
        $attributes['required'] = 'required';
    }
@endphp

@include('form.file',['name'=>'logo','value'=> $row->logo ?? null,'attributes'=>$attributes])
@if(!isset($row->id))
@include('form.input',['type'=>'text','name'=>'manager_id','value'=> $row->manager_email ?? null,
'attributes'=>['class'=>'form-control','label'=>trans('school-accounts.manager id'),'placeholder'=>trans('school-accounts.manager id'),'required'=>'required']])

@endif

@php
$disabled=false;
if(isset($row->id)){
    $disabled=true;
}
@endphp
@include('form.select',['name'=>'country_id','options'=> $countries , $row->country_id ?? null ,
'attributes'=>['id'=>'country_id','class'=>'form-control','required'=>'required','label'=>trans('school-accounts.Country'),'placeholder'=>trans('school-accounts.Country'),'disabled'=>$disabled]])

@include('form.multiselect',['name'=>'educational_systems[]','error_name'=>'educational_systems','options'=> $educationalSystems?? [] , 'value'=> $row->educationalSystems ?? [] ,
'attributes'=>['id'=>'educational_system_id','class'=>'form-control select2','label'=>trans('school-accounts.Educational Systems'),'disabled'=>$disabled ,'old_data' => json_encode( old('educational_systems')) ?? '' ]])

@include('form.multiselect',['name'=>'grade_classes[]','error_name'=>'grade_classes','options'=> $gradeClasses ?? [] , 'value'=> $row->gradeClasses ?? [] ,
'attributes'=>['id'=>'grade_class_id','class'=>'form-control select2','label'=>trans('school-accounts.Grade Classes'),'disabled'=>$disabled  ,'old_data' => json_encode( old('grade_classes')) ?? '']])

@include('form.multiselect',['name'=>'educational_terms[]','error_name'=>'educational_terms','options'=> $educationalTerms , 'value'=> $row->educationalTerms ?? [] ,
'attributes'=>['id'=>'educational_term_id','class'=>'form-control educational select2','label'=>trans('school-accounts.Educational Terms'),'disabled'=>$disabled]])

@include('form.multiselect',['name'=>'academical_years[]','error_name'=>'academical_years','options'=> $academicalYears , 'value'=> $row->academicYears ?? [] ,
'attributes'=>['id'=>'academical_year_id','class'=>'form-control select2','label'=>trans('school-accounts.Academic years'),'disabled'=>$disabled]])
<div id="logo"></div>


@php
    $attributes=['class'=>'form-control','label'=>trans('users.Is active'),'required'=>1];
@endphp
@include('form.boolean',['name'=>'is_active','value'=>$row->is_active ?? null,'attributes'=>$attributes])

@push('js')
    <script>

        function getEducational(value)
        {
            $.get('{{ route('admin.educationalSystems.get.educational.system') }}',
            {
                '_token': $('meta[name=csrf-token]').attr('content'),
                country_id: value,
            })
            .done(function(response){
                @if(!isset($row->educational_system_id))
                $('#educational_system_id option').remove();
                    @endif
                let options = '';
                for (let d in response.educationSystem) {
                    options += buildSelectOption(d, response.educationSystem[d], '') + '\n';
                }
                $('#educational_system_id').html(options);
                if($('#country_id').val() !== ''){
                $('#educational_system_id').val(JSON.parse( $('#educational_system_id').attr('old_data'))).trigger('change');
                }
            });

        }

        function getCountries(value)
        {
            $.get('{{ route('admin.gradeClasses.get.grade.classes') }}',
            {
                '_token': $('meta[name=csrf-token]').attr('content'),
                country_id: value,
            })
            .done(function(response){
                @if(!isset($row->grade_class_id))
                $('#grade_class_id option').remove();
                    @endif
                let options = '';
                for (let d in response.gradeClasses) {
                    options += buildSelectOption(d, response.gradeClasses[d], '') + '\n';
                }
                $('#grade_class_id').html(options);
                if($('#country_id').val() !== ''){
                    $('#grade_class_id').val(JSON.parse( $('#grade_class_id').attr('old_data'))).trigger('change');
                    }
            });
        }

        function buildSelectOption(key, value, selected) {
            return "<option value='" + key + "'" + selected + ">" + value + "</option>";
        }

        $(document).ready(function () {

            getEducational($('#country_id').val());
            getCountries($('#country_id').val());

            $('#country_id').change(function () {

             getEducational(this.value);
             getCountries(this.value);

            });
        });
    </script>

@endpush
