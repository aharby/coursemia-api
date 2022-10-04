@foreach(config("translatable.locales") as $lang)
    @php
        $attributes=['class'=>'form-control','label'=>trans('academic_years.name').' '.$lang,'placeholder'=>trans('academic_years.name').' '.$lang,'required'=>1];
    @endphp
    @include('form.input',['type'=>'text','name'=>'name:'.$lang,'value'=> $row->name[$lang] ?? null,'attributes'=>$attributes])
@endforeach

@include('form.select',['name'=>'country_id' , 'value'=> $row->country_id ?? null , 'options'=> $countries , 'attributes'=>['id'=>'country_id','class'=>'form-control','required'=>'required','label'=>trans('academic_years.Country'),'placeholder'=>trans('academic_years.Country')]])

@include('form.select',['name'=>'educational_system_id','value' => $row->educational_system_id ?? null ,'options'=> $educationalSystem , 'attributes'=>['id'=>'educational_system_id','class'=>'form-control educational','required'=>'required','label'=>trans('app.Educational System'),'placeholder'=>trans('app.Educational System')]])


@php
    $attributes=['class'=>'form-control datepicker' ,'onkeydown'=>'return false' ,'required'=>1,'label'=>trans('academic_years.end date'),'placeholder'=>trans('academic_years.end date')];
@endphp

@include('form.input',['name'=>'end_date','type'=>'text', 'value' => $row->end_date ?? null,'attributes'=>$attributes])


@php
    $attributes=['class'=>'form-control','label'=>trans('academic_years.Is active'),'required'=>1];
@endphp
@include('form.boolean',['name'=>'is_active',$attributes])




@push('js')
    <script>
        $(document).ready(function () {
            $('#country_id').change(function () {
                $.get('{{ route('admin.academicYears.get.educational.system') }}',
                    {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        country_id: this.value,
                    })
                    .done(function(response){
                        $('#educational_system_id option').remove();
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
        })
    </script>
@endpush
