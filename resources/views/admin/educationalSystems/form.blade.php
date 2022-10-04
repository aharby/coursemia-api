@foreach(config("translatable.locales") as $lang)
    @php
        $attributes=['class'=>'form-control','label'=>trans('educational_systems.name').' '.$lang,'placeholder'=>trans('educational_systems.name').' '.$lang,'required'=>1];
    @endphp
    @include('form.input',['type'=>'text','name'=>'name:'.$lang,'value'=> $row->name[$lang] ?? null,'attributes'=>$attributes])
@endforeach

@include('form.select',['name'=>'country_id','options'=> $countries , $row->country_id ?? null ,'attributes'=>['id'=>'country_id','class'=>'form-control','required'=>'required','label'=>trans('educational_systems.Country'),'placeholder'=>trans('educational_systems.Country')]])

@php
    $attributes=['class'=>'form-control','label'=>trans('educational_systems.Is active'),'required'=>1];
@endphp
@include('form.boolean',['name'=>'is_active',$attributes])
