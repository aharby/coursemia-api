@php
    $attributes=['id'=>'picture','class'=>'form-control','label'=>trans('users.Picture'),'placeholder'=>trans('users.Picture'),'file_type' => 'image', 'required' => $row->id ? false : true];
@endphp
@include('form.file',['name'=>'picture', 'value' => $row->picture, 'attributes'=>$attributes])



@foreach(config("translatable.locales") as $lang)
    @include('form.input',['name'=>'name:'.$lang,
        'value'=> $row->translateOrDefault($lang)->name ?? null,
        'type'=>'text','attributes'=>['class'=>'form-control',
        'label'=>trans('psychological_tests.Name').' '.$lang,
        'placeholder'=>trans('psychological_tests.Name').' '.$lang,
        'required'=>1 ,
        'max' => 191]])
@endforeach

@foreach(config("translatable.locales") as $lang)
    @include('form.input',['name'=>'instructions:'.$lang,
        'value'=> $row->translateOrDefault($lang)->instructions ?? null,
        'type'=>'textarea','attributes'=>['class'=>'form-control',
        'label'=>trans('psychological_tests.Instructions').' '.$lang,
        'placeholder'=>trans('psychological_tests.Instructions').' '.$lang,
        'required'=>1]])
@endforeach

@php
    $attributes=['class'=>'form-control','label'=>trans('psychological_tests.Is active'),'required'=>1];
@endphp
@include('form.boolean',['name'=>'is_active',$attributes])

