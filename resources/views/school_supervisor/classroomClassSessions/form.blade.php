
{{-- Program, From To Fields START --}}
<div id="js-availability_information">

    <div class="form-group">
        {!! Form::label('instructor_id', __("sessions.Choose Instructor"), ['class' => 'control-label required']) !!}
        {!!
            Form::select('instructor_id',
                $instructors ?? [],
                old("instructor_id", null),
                [
                    'class' => 'form-control',
                    'id' => 'instructor_id',
                    'required' => 'required',
                    'autocomplete' => 'off',
                    'placeholder' => __("sessions.Choose Instructor")
                ])
        !!}
    </div>



</div>
{{-- Program, From To Fields END --}}

{{-- START Form Buttons --}}
<div class="form-group">
    <button type="submit" class="btn btn-success">{{ __("subject.Save") }}</button>
</div>
{{-- END   Form Buttons --}}
