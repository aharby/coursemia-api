@include('form.input',['type'=>'text','name'=>'name','value'=> $row->name ?? null,
'attributes'=>['class'=>'form-control','label'=>trans('subjects.name'),'placeholder'=>trans('subjects.name'),'required'=>'required']])

@if((isset($row) && $row->is_aptitude == 0) || Route::current()->getName() == 'admin.subjects.get.create')

    @include('form.select',['name'=>'country_id','options'=> $countries , $row->country_id ?? null ,
    'attributes'=>['id'=>'country_id','class'=>'form-control','required'=>'required','label'=>trans('educational_systems.Country'),'placeholder'=>trans('subjects.Country')]])

    @include('form.select',['name'=>'educational_system_id','options'=> $educationalSystemRepository , 'value'=> $row->educational_system_id ?? null ,
    'attributes'=>['id'=>'educational_system_id','class'=>'form-control educational','required'=>'required','label'=>trans('subjects.Educational System'),'placeholder'=>trans('subjects.Educational System')]])

    @include('form.select',['name'=>'grade_class_id','options'=> $gradeClasses , 'value'=> $row->grade_class_id ?? null ,
    'attributes'=>['id'=>'grade_class_id','class'=>'form-control ','required'=>'required','label'=>trans('subjects.Grade Class'),'placeholder'=>trans('subjects.Grade Class')]])

    @include('form.select',['name'=>'educational_term_id','options'=> $educationalTerms , 'value'=> $row->educational_term_id ?? 'null' ,
    'attributes'=>['id'=>'educational_term_id','class'=>'form-control educational','label'=>trans('subjects.Educational Term'),'placeholder'=>trans('subjects.Educational Term')]])

    @include('form.select',['name'=>'academical_years_id','options'=> $academicalYears , 'value'=> $row->academical_year_id ?? 'null' ,
    'attributes'=>['id'=>'academical_year_id','class'=>'form-control ','label'=>trans('subjects.Academic year'),'placeholder'=>trans('subjects.Academic year')]])

    @include('form.input',['type'=>'text','name'=>'subscription_cost','value'=> $row->subscription_cost ?? null,
    'attributes'=>['class'=>'form-control','label'=>trans('subjects.Subscription Cost'),'placeholder'=>trans('subjects.Subscription Cost'),'required'=>'required']])


    @include('form.input',['type'=>'text','name'=>'start_date','value'=> $row->start_date ?? null,
    'attributes'=>['class'=>'form-control datepicker','label'=>trans('subjects.Start Date'),'placeholder'=>trans('subjects.Start Date')]])


    @include('form.input',['type'=>'text','name'=>'end_date','value'=> $row->end_date ?? null,
    'attributes'=>['class'=>'form-control datepicker','label'=>trans('subjects.End Date'),'placeholder'=>trans('subjects.End Date')]])

@endif

@include('form.select',['name'=>'sme_id','options'=> $smes , 'value'=> $row->sme_id ?? 'null' ,
'attributes'=>['id'=>'sme_id','class'=>'form-control ','label'=>trans('subjects.SME'),'placeholder'=>trans('subjects.SME')]])

@php
    $attributes=['id'=> 'image', 'class'=>'form-control','label'=>trans('subjects.Image')];
@endphp
@include('form.file',['name'=>'image','value'=>$row->image ?? null,'attributes'=>$attributes])


@include('form.input',['type'=>'color','name'=>'color','value'=> $row->color ?? null,
'attributes'=>['class'=>'form-control','label'=>trans('subjects.color'),'placeholder'=>trans('subjects.color')]])


@include('form.multiselect',['name'=>'content_authors[]','options'=> $contentAuthors , 'value'=> $selectedContentAuthors ??[] ,
'attributes'=>['id'=>'content_authors_id','class'=>'form-control select2','label'=>trans('subjects.Content Authors')]])

@include('form.multiselect',['name'=>'instructors[]','options'=> $instructors, 'value'=> $selectedInstructors ?? [],
'attributes'=>['id'=>'instructors_id','class'=>'form-control select2','label'=>trans('subjects.Instructors')]])

@if((isset($row) && $row->is_aptitude == 0) || Route::current()->getName() == 'admin.subjects.get.create')
    @php
        $attributes=['class'=>'form-control','label'=>trans('subjects.Is active'),'required'=>1];
    @endphp
    @include('form.boolean',['name'=>'is_active',$attributes])
@endif

@include('form.boolean',['name'=>'is_top_qudrat','attributes'=> ['class'=>'form-control','label'=>trans('subjects.is_top_qudrat'),'required'=>1]])


@include('form.select',['name'=>'direction','options'=> $directions , 'value'=> $row->direction ?? null ,
    'attributes'=>['id'=>'direction','class'=>'form-control ','required'=>'required','label'=>trans('subjects.Direction'),'placeholder'=>trans('subjects.Direction')]])


@push('js')
    <script>
        {{-- select2 --}}

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




