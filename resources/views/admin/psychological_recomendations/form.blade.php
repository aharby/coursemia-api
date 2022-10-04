@foreach(config("translatable.locales") as $lang)
    @include('form.input',['name'=>'result:'.$lang,
        'value'=> $row->translateOrDefault($lang)->result ?? null,
        'type'=>'textarea','attributes'=>['class'=>'form-control',
        'label'=>trans('psychological_recomendations.Result').' '.$lang,
        'placeholder'=>trans('psychological_recomendations.Result').' '.$lang,
        'required'=>1 ,
        'max' => 191]])
@endforeach

@foreach(config("translatable.locales") as $lang)
    @include('form.input',['name'=>'recomendation:'.$lang,
        'value'=> $row->translateOrDefault($lang)->recomendation ?? null,
        'type'=>'textarea','attributes'=>['class'=>'form-control',
        'label'=>trans('psychological_recomendations.Recomendation').' '.$lang,
        'placeholder'=>trans('psychological_recomendations.Recomendation').' '.$lang,
        'required'=>1]])
@endforeach


@include('form.input',['type'=>'text','name'=>'from','value'=> $row->from ?? null,
'attributes'=>['class'=>'form-control','label'=>trans('psychological_recomendations.From'),'placeholder'=>trans('psychological_recomendations.From'),'required'=>'required']])


@include('form.input',['type'=>'text','name'=>'to','value'=> $row->to ?? null,
'attributes'=>['class'=>'form-control','label'=>trans('psychological_recomendations.To'),'placeholder'=>trans('psychological_recomendations.To'),'required'=>'required']])


@php
    $attributes=['class'=>'form-control','label'=>trans('app.Is active'),'required'=>1];
@endphp
@include('form.boolean',['name'=>'is_active',$attributes])

