@foreach(config("translatable.locales") as $lang)
    @php
        $attributes=['class'=>'form-control','label'=>trans('countries.name').' '.$lang,'placeholder'=>trans('countries.name').' '.$lang,'required'=>1];
    @endphp
    @include('form.input',['type'=>'text','name'=>'name:'.$lang,'value'=> $row->name[$lang] ?? null,'attributes'=>$attributes])
@endforeach
@foreach(config("translatable.locales") as $lang)
    @php
        $attributes=['class'=>'form-control','label'=>trans('countries.currency').' '.$lang,'placeholder'=>trans('countries.currency').' '.$lang,'required'=>1];
    @endphp
    @include('form.input',['type'=>'text','name'=>'currency:'.$lang,'value'=> $row->currency[$lang] ?? null,'attributes'=>$attributes])
@endforeach
@php
    $attributes=['class'=>'form-control','label'=>trans('countries.Country Code'),'placeholder'=>trans('countries.Country Code'),'required'=>1];
@endphp

@include('form.input',['type'=>'text','name'=>'country_code','value'=> $row->code ?? null,'attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('countries.Is active'),'required'=>1];
@endphp
@include('form.boolean',['name'=>'is_active',$attributes])
