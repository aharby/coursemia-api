
@php
    $attributes=['id'=> 'image', 'class'=>'form-control','label'=>trans('certificates.Image')];
@endphp
@include('form.file',['name'=>'image','value'=>$row->image ?? null,'attributes'=>$attributes])


@include('form.input',['type'=>'text','name'=>'attributes[name][x]','value'=> old("attributes.name.x", $row->attributes["name"]["x"]?? null),
'attributes'=>['class'=>'form-control','label'=>trans('certificates.Student name X'),'placeholder'=>trans('certificates.Student name X'),'required'=>'required']])
@include('form.input',['type'=>'text','name'=>'attributes[name][x2]','value'=> old("attributes.name.x", $row->attributes["name"]["x2"]?? null),
'attributes'=>['class'=>'form-control','label'=>trans('certificates.Student name X'),'placeholder'=>trans('certificates.Student name X'),'required'=>'required']])
@include('form.input',['type'=>'text','name'=>'attributes[name][y]','value'=> old("attributes.name.y", $row->attributes["name"]["y"]?? null),
'attributes'=>['class'=>'form-control','label'=>trans('certificates.Student name Y'),'placeholder'=>trans('certificates.Student name Y'),'required'=>'required']])
@include('form.input',['type'=>'text','name'=>'attributes[name][font_size]','value'=> old("attributes.name.font_size", $row->attributes["name"]["font_size"]?? null),
'attributes'=>['class'=>'form-control','label'=>trans('certificates.Student name Font Size'),'placeholder'=>trans('certificates.Student name Font Size'),'required'=>'required']])


@include('form.input',['type'=>'text','name'=>'attributes[teacher][x]','value'=> old("attributes.teacher.x", $row->attributes["teacher"]["x"]?? null),
'attributes'=>['class'=>'form-control','label'=>trans('certificates.Teacher name X'),'placeholder'=>trans('certificates.Teacher name'),'required'=>'required']])
@include('form.input',['type'=>'text','name'=>'attributes[teacher][x2]','value'=> old("attributes.teacher.x", $row->attributes["teacher"]["x2"]?? null),
'attributes'=>['class'=>'form-control','label'=>trans('certificates.Teacher name X'),'placeholder'=>trans('certificates.Teacher name'),'required'=>'required']])
@include('form.input',['type'=>'text','name'=>'attributes[teacher][y]','value'=> old("attributes.teacher.y", $row->attributes["teacher"]["y"]?? null),
'attributes'=>['class'=>'form-control','label'=>trans('certificates.Teacher name Y'),'placeholder'=>trans('certificates.Teacher name'),'required'=>'required']])
@include('form.input',['type'=>'text','name'=>'attributes[teacher][font_size]','value'=> old("attributes.teacher.font_size", $row->attributes["teacher"]["font_size"]?? null),
'attributes'=>['class'=>'form-control','label'=>trans('certificates.Teacher name Font Size'),'placeholder'=>trans('certificates.Teacher name Font Size'),'required'=>'required']])


@include('form.input',['type'=>'text','name'=>'attributes[subject][x]','value'=> old("attributes.subject.x", $row->attributes["subject"]["x"]?? null),
'attributes'=>['class'=>'form-control','label'=>trans('certificates.Subject name X'),'placeholder'=>trans('certificates.Subject name'),'required'=>'required']])
@include('form.input',['type'=>'text','name'=>'attributes[subject][x2]','value'=> old("attributes.subject.x", $row->attributes["subject"]["x2"]?? null),
'attributes'=>['class'=>'form-control','label'=>trans('certificates.Subject name X'),'placeholder'=>trans('certificates.Subject name'),'required'=>'required']])
@include('form.input',['type'=>'text','name'=>'attributes[subject][y]','value'=> old("attributes.subject.y", $row->attributes["subject"]["y"]?? null),
'attributes'=>['class'=>'form-control','label'=>trans('certificates.Subject name Y'),'placeholder'=>trans('certificates.Subject name'),'required'=>'required']])
@include('form.input',['type'=>'text','name'=>'attributes[subject][font_size]','value'=> old("attributes.subject.font_size", $row->attributes["subject"]["font_size"]?? null),
'attributes'=>['class'=>'form-control','label'=>trans('certificates.Subject name Font Size'),'placeholder'=>trans('certificates.Subject name Font Size'),'required'=>'required']])

