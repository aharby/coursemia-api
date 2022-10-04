@foreach(config("translatable.locales") as $lang)
    @php
        $attributes=['class'=>'form-control','label'=>trans('grade_classes.title').' '.$lang,'placeholder'=>trans('grade_classes.name').' '.$lang,'required'=>1];
    @endphp
    @include('form.input',['type'=>'text','name'=>'title:'.$lang,'value'=> $row->name[$lang] ?? null,'attributes'=>$attributes])
@endforeach

@include('form.select',['name'=>'country_id','options'=> $countries , $row->country_id ?? null ,'attributes'=>['id'=>'country_id','class'=>'form-control','required'=>'required','label'=>trans('educational_systems.Country'),'placeholder'=>trans('educational_systems.Country')]])
@include('form.select',['name'=>'educational_system_id','options'=> $educationalSystemRepository , 'value'=> $row->educational_system_id ?? null ,'attributes'=>['id'=>'educational_system_id','class'=>'form-control educational','required'=>'required','label'=>trans('app.Educational System'),'placeholder'=>trans('app.Educational System')]])

@php
    $attributes=['class'=>'form-control','label'=>trans('grade_classes.Is active'),'required'=>1];
@endphp
@include('form.boolean',['name'=>'is_active','class'=>'form-control','label'=>trans('grade_classes.Is active'),'required'=>1])
@push('js')
    <script>
        $(document).ready(function () {
            $('#country_id').change(function () {
                $.get('{{ route('admin.gradeClasses.get.educational.system') }}',
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
        })
    </script>
@endpush