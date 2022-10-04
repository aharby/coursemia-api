@foreach(config("translatable.locales") as $lang)
    @php
        $attributes=['class'=>'form-control','label'=>trans('schools.name').' '.$lang,'placeholder'=>trans('schools.name').' '.$lang,'required'=>1];
    @endphp
    @include('form.input',['type'=>'text','name'=>'name:'.$lang,'value'=> $row->name[$lang] ?? null,'attributes'=>$attributes])
@endforeach

@include('form.select',['name'=>'country_id','options'=> $countries , $row->country_id ?? null ,'attributes'=>['id'=>'country_id','class'=>'form-control','required'=>'required','label'=>trans('schools.Country'),'placeholder'=>trans('schools.Country')]])

@include('form.select',['name'=>'educational_system_id','options'=> $educationalSystemRepository , 'value'=> $row->educational_system_id ?? null ,'attributes'=>['id'=>'educational_system_id','class'=>'form-control educational','required'=>'required','label'=>trans('schools.Educational System'),'placeholder'=>trans('schools.Educational System')]])


@php
    $attributes=['class'=>'form-control','label'=>trans('schools.Address'),'placeholder'=>trans('schools.Address'),'required'=>1];
@endphp
@include('form.input',['type'=>'text','name'=>'address','value'=> $row->address ?? null,'attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('schools.email'),'placeholder'=>trans('schools.email')];
@endphp
@include('form.input',['type'=>'text','name'=>'email','value'=> $row->email ?? null,'attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('schools.mobile'),'placeholder'=>trans('schools.mobile'),'required'=>1];
@endphp
@include('form.input',['type'=>'text','name'=>'mobile','value'=> $row->mobile ?? null,'attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('schools.Is active'),'required'=>1];
@endphp
@include('form.boolean',['name'=>'is_active','class'=>'form-control','label'=>trans('schools.Is active'),'required'=>1])
@push('js')
    <script>
        $(document).ready(function () {
            $('#country_id').change(function () {
                $.get('{{ route('admin.schools.get.educational.system') }}',
                    {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        country_id: this.value,
                    })
                    .done(function(response){
                        @if(!isset($row->educational_system_id))
                        $('#educational_system_id option').remove();
                        @endif
                        let options = '<option selected="selected" value=""> {{trans('schools.Educational System')}} </option>';
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
