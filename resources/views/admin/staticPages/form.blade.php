@php
    $attributes=['class'=>'form-control','disabled'=> 1 ,'label'=>trans('staticPage.slug'),'placeholder'=>trans('staticPage.slug'),'required'=>1];
@endphp
@include('form.input',['type'=>'text','name'=>'slug','value'=> $row->slug ?? null,'attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('staticPage.url'),'placeholder'=>trans('staticPage.url')];
@endphp
@include('form.input',['type'=>'text','name'=>'url','value'=> $row->url ?? null,'attributes'=>$attributes])


@php
    $attributes=['id'=>'bg_image','class'=>'form-control','label'=>trans('staticPage.background image'),'placeholder'=>trans('staticPage.background image'),'file_type' => 'image'];
@endphp
@include('form.file',['name'=>'bg_image', 'value' => $row->bg_image, 'attributes'=>$attributes])


@foreach(config("translatable.locales") as $lang)
    @include('form.input',['name'=>'title:'.$lang,
        'value'=> $row->translateOrDefault($lang)->title ?? null,
        'type'=>'text','attributes'=>['class'=>'form-control',
        'label'=>trans('staticPage.title').' '.$lang,
        'placeholder'=>trans('staticPage.title').' '.$lang,
        'required'=>1 ,
        'max' => 191]])
@endforeach

@foreach(config("translatable.locales") as $lang)
    @include('form.input',['name'=>'body:'.$lang,
        'value'=> $row->translateOrDefault($lang)->body ?? null,
        'type'=>'textarea','attributes'=>['class'=>'form-control',
        'label'=>trans('staticPage.body').' '.$lang,
        'placeholder'=>trans('staticPage.body').' '.$lang,
        // 'required'=>1 commented till fixing rich text editor: not focusable issue
        ]])
@endforeach



@include('form.boolean',['name'=>'is_active', 'attributes' => ['class'=>'form-control' ,'label'=>trans('staticPage.Is active')]])
