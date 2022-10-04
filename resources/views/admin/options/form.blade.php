@foreach(config("translatable.locales") as $lang)
    @php
        $attributes=['class'=>'form-control','label'=>trans('options.title').' '.$lang,'placeholder'=>trans('schools.title').' '.$lang,'required'=>1];
    @endphp
    @include('form.input',['type'=>'text','name'=>'title:'.$lang,'value'=> $row->title[$lang] ?? null,'attributes'=>$attributes])
@endforeach

@if(!isset($row->title))
    @include('form.select',['name'=>'type','options'=>$optionTypes,'attributes'=>['class'=>'form-control select2','label'=>trans('options.Type'),'placeholder'=>trans('options.Select type'), 'required'=> 1]])
@endif

@php
    $attributes=['class'=>'form-control','label'=>trans('schools.Is active'),'required'=>1];
@endphp
@include('form.boolean',['name'=>'is_active','class'=>'form-control','label'=>trans('schools.Is active'),'required'=>1])
