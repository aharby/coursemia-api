@if(isset($row))
    <div class="row mg-t-20 form-group">
        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name" >
            {{ trans('users.Type') }}
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <h5>{{ $row->type }}</h5>
        </div>
    </div>
@else
    @include('form.select',['name'=>'type','options'=> $userType , $row->type ?? null ,'attributes'=>['id'=>'type','class'=>'form-control','required'=>'required','label'=>trans('users.Type'),'placeholder'=>trans('users.Type')]])
@endif

@php
    $attributes=['class'=>'form-control','label'=>trans('users.First name'),'placeholder'=>trans('users.First name'),'required'=>1];
@endphp
@include('form.input',['type'=>'text','name'=>'first_name','value'=> $row->first_name ?? null,'attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('users.Last name'),'placeholder'=>trans('users.Last name'),'required'=>1];
@endphp
@include('form.input',['type'=>'text','name'=>'last_name','value'=> $row->last_name ?? null,'attributes'=>$attributes])


@php
    $attributes=['class'=>'form-control','label'=>trans('users.Mobile'),'placeholder'=>trans('users.Mobile'), 'required'=>1];
@endphp
@include('form.input',['type'=>'text','name'=>'mobile','value'=> $row->mobile ?? null,'attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('users.Email'),'placeholder'=>trans('users.Email')];
@endphp
@include('form.input',['type'=>'text','name'=>'email','value'=> $row->email ?? null,'attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('users.Password'),'placeholder'=>trans('users.Password'),'required'=>@$row->id ? 0 : 1];
@endphp

@include('form.password',['name'=>'password','attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('users.Password confirmation'),'placeholder'=>trans('users.Password confirmation'),'required'=>@$row->id ? 0 : 1];
@endphp

@include('form.password',['name'=>'password_confirmation','attributes'=>$attributes])



@php
    $attributes=['class'=>'form-control','label'=>trans('users.Profile Picture') . ' (' . trans('users.image dimensions') . ')'];
@endphp
@include('form.file',['name'=>'profile_picture','value'=>$row->profile_picture ?? null,'attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('users.Is active'),'required'=>1];
@endphp
@include('form.boolean',['name'=>'is_active','value'=>$row->is_active ?? null,'attributes'=>$attributes])

@include('admin.users.content_author')

@include('admin.users.instructor')

<div class="type type-student_teacher type-student" style="display: none">
    @include('form.select',['name'=>'country_id','options'=> $countries , 'value'=> $row->country_id ?? null ,
 'attributes'=>['id'=>'country_id','class'=>'form-control','label'=>trans('app.Country'),'placeholder'=>trans('app.Country')]])
</div>
@include('admin.users.student_teacher')

@includeWhen(!isset($row) or $row->type == $userEnums::STUDENT_TYPE,'admin.users.student')

<div class="type type-student_teacher type-school_admin" style="display: none">
    @php
        $attributes=['class'=>'form-control','label'=>trans('users.schools')];
    @endphp
    @include('form.multiselect',['name'=>'schools[]','options'=> $school_accounts ,'value'=>@$row ? $row->schoolAdminAssignedSchools->pluck('id','name')->toArray() :[],'attributes'=>$attributes])</div>
@push('js')
    <script>
        $(function() {
            $('#type').change(function(e) {
                var type = $(this).val();
                $(".type").hide();
                $(".type-"+type).show();
            });
            $('#type').trigger('change');
            @if(isset($row) && $row->type)
            $(".type-" + "{{ $row->type }}").show();
            @endif
        });
    </script>
@endpush
