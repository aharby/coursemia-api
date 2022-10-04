@include('form.input',['type'=>'text','name'=>'name','value'=> $row->name ?? null,
'attributes'=>['class'=>'form-control','label'=>trans('live_sessions.name'),'placeholder'=>trans('live_sessions.name'),'required'=>'required']])


@include('form.select',['name'=>'subject_id','options'=> $subjects , $row->subject_id ?? null ,
'attributes'=>['id'=>'subject_id','class'=>'form-control','label'=>trans('live_sessions.Subject'),'placeholder'=>trans('live_sessions.Subject'), 'required' => true]])


@include('form.input',['type'=>'text','name'=>'subscription_cost','value'=> $row->subscription_cost ?? null,
'attributes'=>['class'=>'form-control','label'=>trans('live_sessions.Subscription Cost'),'placeholder'=>trans('live_sessions.Subscription Cost'),'required'=>'required']])

@include('form.select',['name'=>'instructor_id','options'=> $instructors , 'value'=> $row->instructor_id ?? 'null' ,
'attributes'=>['id'=>'instructor_id','class'=>'form-control ','label'=>trans('live_sessions.Instructor'),'placeholder'=>trans('live_sessions.Instructor'),'required'=>'required']])

@php
    $attributes=['id'=>'picture','class'=>'form-control','label'=>trans('users.Picture'),'placeholder'=>trans('users.Picture'), 'required' => $row->id ? false : true];
@endphp
@include('form.file',['name'=>'picture','value' => $row->picture ?? null, 'attributes'=>$attributes])



	{{-- session form --}}
	@include('admin/liveSessions/sessionsForm', ['row' => $row->session ?? null])


@php
    $attributes=['class'=>'form-control','label'=>trans('live_sessions.Is active'),'required'=>1];
@endphp
@include('form.boolean',['name'=>'is_active',$attributes])


@push('js')
    <script>
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



