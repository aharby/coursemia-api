@foreach(config("translatable.locales") as $lang)
    @include('form.input',['name'=>'name:'.$lang,
        'value'=> $row->translateOrDefault($lang)->name ?? null,
        'type'=>'text','attributes'=>['class'=>'form-control',
        'label'=>trans('psychological_tests.Name').' '.$lang,
        'placeholder'=>trans('psychological_tests.Name').' '.$lang,
        'required'=>1 ,
        'max' => 191]])
@endforeach

@include('form.input',['type'=>'text','name'=>'points','value'=> $row->points ?? null,
'attributes'=>['class'=>'form-control','label'=>trans('psychological_tests.points'),'placeholder'=>trans('psychological_tests.points'),'required'=>'required']])

@php
    $attributes=['class'=>'form-control','label'=>trans('psychological_tests.Is active'),'required'=>1];
@endphp
@include('form.boolean',['name'=>'is_active',$attributes])

