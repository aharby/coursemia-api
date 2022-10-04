@include('form.input',['type'=>'text','name'=>'name','value'=> $row->name ?? null,
'attributes'=>['class'=>'form-control','label'=>trans('courses.name'),'placeholder'=>trans('courses.name'),'required'=>'required']])

@include('form.input',['type'=>'text','name'=>'description','value'=> $row->description ?? null,
'attributes'=>['class'=>'form-control','label'=>trans('courses.description'),'placeholder'=>trans('courses.description'),'required'=>'required']])


@include('form.select',['name'=>'type','options'=> $types , $row->type ?? null ,
'attributes'=>['id'=>'type','class'=>'form-control','label'=>trans('courses.Type'),'placeholder'=>trans('courses.Type'),'required'=>'required' , 'instructors' => json_encode($instructors)]])

<span class="subject_id">

@include('form.select',['name'=>'subject_id','options'=> $subjects , $row->subject_id ?? null ,
'attributes'=>['id'=>'subject_id','class'=>'form-control','label'=>trans('courses.Subject'),'placeholder'=>trans('courses.Subject')]])

</span>

@include('form.input',['type'=>'text','name'=>'subscription_cost','value'=> $row->subscription_cost ?? null,
'attributes'=>['class'=>'form-control','label'=>trans('courses.Subscription Cost'),'placeholder'=>trans('courses.Subscription Cost'),'required'=>'required']])


@include('form.input',['type'=>'text','name'=>'start_date','value'=> $row->start_date ?? null,
'attributes'=>['class'=>'form-control nowdatepicker','label'=>trans('courses.Start Date'),'placeholder'=>trans('courses.Start Date'),'required'=>'required']])


@include('form.input',['type'=>'text','name'=>'end_date','value'=> $row->end_date ?? null,
'attributes'=>['class'=>'form-control nowdatepicker','label'=>trans('courses.End Date'),'placeholder'=>trans('courses.End Date'),'required'=>'required']])

@include('form.select',['name'=>'instructor_id','options'=> $instructors , 'value'=> $row->instructor_id ?? 'null' ,
'attributes'=>['id'=>'instructor_id','class'=>'form-control ','label'=>trans('courses.Instructor'),'placeholder'=>trans('courses.Instructor'),'required'=>'required']])


@php
    $attributes=['id'=>'picture','class'=>'form-control','label'=>trans('users.Picture') . ' (' . trans('courses.image diminssion') . ')','placeholder'=>trans('users.Picture'), 'required' => $row->id ? false : true];
@endphp
@include('form.file',['name'=>'picture', 'value'=>$row->picture ?? null ,'attributes'=>$attributes])

@php
    $attributes=['id'=>'medium_picture','class'=>'form-control','label'=>trans('courses.medium_picture') . ' (' . trans('courses.image medium diminssion') . ')','placeholder'=>trans('users.Picture'), 'required' => $row->id ? false : true];
@endphp
@include('form.file',['name'=>'medium_picture', 'value'=>$row->medium_picture ?? null ,'attributes'=>$attributes])

@php
    $attributes=['id'=>'small_picture','class'=>'form-control','label'=>trans('courses.small_picture') . ' (' . trans('courses.image small diminssion') . ')','placeholder'=>trans('users.Picture'), 'required' => $row->id ? false : true];
@endphp
@include('form.file',['name'=>'small_picture', 'value'=>$row->small_picture ?? null ,'attributes'=>$attributes])

@php
    $attributes=['class'=>'form-control','label'=>trans('courses.Is active'),'required'=>1];
@endphp
@include('form.boolean',['name'=>'is_active',$attributes])


{{--@php--}}
{{--    $attributes=['class'=>'form-control','label'=>trans('courses.Is Top Qudrat'),'required'=>1];--}}
{{--@endphp--}}
{{--@include('form.boolean',['name'=>'is_top_qudrat',$attributes])--}}


@if(! $row->id)
	@include('admin/courses/sessionsForm')
@endif
@php $dataArray = json_encode($instructors) ;@endphp
@push('js')

	<script>
		$(function() {
	        //user_type
	        $('#type').change(function(e) {

	           var type = $(this).val();

	           if(type == 'public_course')
	           {
	           		$(".subject_id").hide();
                       console.log();
                       let instructors = JSON.parse($(this).attr('instructors'));

                       let options = '<option selected="selected" value=""> {{ trans('vcr_schedule.Instructor') }} </option>';
                       $.each(instructors,function(key,value){
                        "<option value='" + key + "'" + selected + ">" + value + "</option>";
                       });
                       $('#instructor_id').html(options);

	           } else {
	           		$(".subject_id").show();
	           }

	        });

	        $('#type').trigger('change');
	    });


        $(document).ready(function () {
            // get Instructors System by subject id
            $('#subject_id').change(function () {
                $.get('{{ route('admin.users.get.instructors') }}',
                    {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        subject_id: this.value,
                    })
                    .done(function(response){
                        @if(!isset($row->instructor_id))
                        $('#instructor_id option').remove();
                        @endif
                        let options = '<option selected="selected" value=""> {{ trans('vcr_schedule.Instructor') }} </option>';
                        for (let d in response.instructors) {
                            options += buildSelectOption(d, response.instructors[d], '') + '\n';
                        }
                        $('#instructor_id').html(options);
                    });

                function buildSelectOption(key, value, selected) {
                    return "<option value='" + key + "'" + selected + ">" + value + "</option>";
                }


            });
        });
    </script>
@endpush
