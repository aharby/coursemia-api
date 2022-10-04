@if($classRoomClass->id)
    <input type="hidden" id="availability_id" name="id" value="{{$classRoomClass->id}}">
@endif

<input type="hidden" name="channelId" id="channelId" value="{{ \Illuminate\Support\Str::uuid() }}" >
<input type="hidden" id="csrfTokenSocket" value="{{ $token }}" >

{{-- Program, From To Fields START --}}
<div id="js-availability_information">
    {{-- START Program Selection Field --}}
    <div class="form-group">
        {!! Form::label('subject_id', __("sessions.Choose Subject"), ['class' => 'control-label required']) !!}
        {!!
            Form::select('subject_id', $subjects , $classRoomClass->program_id , [
                'class' => 'form-control select2',
                'id' => 'subject_id',
                'required' => 'required',
                'autocomplete' => 'off',
                'placeholder' => __("sessions.Choose Subject")
            ])
        !!}
    </div>
    {{-- END   Program Selection Field --}}

    <div class="form-group">
        {!! Form::label('instructor_id', __("sessions.Choose Instructor"), ['class' => 'control-label required']) !!}
        {!!
            Form::select('instructor_id', $instructors ?? [] , $classRoomClass->instructor_id , [
                'class' => 'form-control select2',
                'id' => 'instructor_id',
                'required' => 'required',
                'autocomplete' => 'off',
                'placeholder' => __("sessions.Choose Instructor")
            ])
        !!}
    </div>

    <div class="form-group">



    </div>

    {{-- START From + Time Fields --}}
    <div class="form-group">
        <div class="input-group">

            <div class="input-group-addon">
                <div class="input-group-text h-100">
                    {{ __("sessions.start Date") }}
                </div>
            </div>

            {!!
                Form::text('from', $classRoomClass->from ? $classRoomClass->from->format('Y-m-d') : null , [
                    'required' => 'required',
                    'class' => 'form-control datepicker',
                    'id' => 'from-datepicker',
                    // Check if date matching DD-MM-YYYY Pattern

                    'placeholder' => __("sessions.start Date"),
                    'autocomplete' => "off"
                ])
            !!}

        </div>
    </div>
    {{-- END   From + Time Fields --}}

    {{-- START To + Time Fields --}}
    <div class="form-group">
        <div class="input-group">

{{--            <div class="input-group-addon">--}}
{{--                <div class="input-group-text ">--}}
{{--                    {{ __("Date to") }}--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            {!!--}}
{{--                Form::text('to', $classRoomClass->to_to_date_string , [--}}
{{--                    'required' => 'required',--}}
{{--                    'class' => 'form-control datepicker',--}}
{{--                    'id' => 'to-datepicker',--}}
{{--                    // Check if date matching DD-MM-YYYY Pattern--}}

{{--                    'placeholder' => __("Date to"),--}}
{{--                    'autocomplete' => "off"--}}
{{--                ])--}}
{{--            !!}--}}
            <div class="input-group-addon">
                <div class="input-group-text h-100">
                    {{ __("sessions.Time From") }}
                </div>
            </div>

            {!!
                Form::text('from_time', $classRoomClass->from_time, [
                    //'required' => 'required',
                    'class' => 'form-control timepicker timepicker-av',
                    'id'    => 'from_time',
                    // Check if date matching hh:mm A Pattern

                    'placeholder' => __("sessions.Time From"),
                    'data-default-time' => $classRoomClass->from_time,
                    'autocomplete' => "off"
                ])
            !!}

            <div class="input-group-addon">
                <div class="input-group-text h-100">
                    {{ __("sessions.Time To") }}
                </div>
            </div>

            {!!
                Form::text('to_time', $classRoomClass->to_time, [
                    //'required' => 'required',
                    'class' => 'form-control timepicker timepicker-av',
                    'id'    => 'to_time',
                    // Check if date matching hh:mm A Pattern

                    'placeholder' => __("sessions.Time To"),
                    'autocomplete' => "off"
                ])
            !!}

{{--            @include('components.field_error', ['name' => 'repetition_times'])--}}
        </div>
        @include('school_supervisor.classroomClasses.partials.error', ['name' => 'start_date'])
    </div>
    {{-- END   To + Time Fields --}}


    {{-- END   From and To Fields Group --}}
    <div class="form-group">

        <h4>{{ __("sessions.Availability Repetition") }}</h4>
        {{-- START Repeat Field --}}

        {{-- START Program Repeat By Field --}}
{{--        {{ dd(request()->old('repeat') > 0 ? request()->old('repeat') :  $classRoomClass->repeat) }}--}}
        <div class="form-group">
            {!! Form::label('repeat', __("sessions.Choose pattern"), ['class' => 'control-label required']) !!}
            {!!
                Form::select('repeat', App\OurEdu\SchoolAccounts\Enums\ClassroomClassEnum::getRepeatBy() ,request()->old('repeat') > 0 ? request()->old('repeat') :  $classRoomClass->repeat  , [
                    'class' => 'form-control repeat_by',
                    'id' => 'repeat_by',
                    'required' => 'required',
                    'placeholder' => __("sessions.Repeat by"),
                    'autocomplete' => "off"
                ])
            !!}
{{--            @include('components.field_error',['name' => 'program_id'])--}}
        </div>

        <div class="form-group" id="repeat_frequency">
            <div class="input-group">


                {{-- START Repetition Times Field --}}

                {{-- START Until Day Field --}}
                {!!
                    Form::text('until_date', $classRoomClass->until_date ? $classRoomClass->until_date->format('Y-m-d') : null, [
                        'class' => 'form-control datepicker',
                        'id' => 'until_date',
                        'placeholder' => __("sessions.Repeat until date"),
                        'autocomplete' => "off"
                    ])
                !!}
                {{-- END   Until Day Field --}}

{{--                @include('components.field_error', ['name' => 'repetition_times'])--}}
            </div>
        </div>

        <div id="repetition_days">
            <hr>

            {{-- START Sunday Group --}}
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon" style="width: 120px">
                        <div class="input-group-text">
                            {{-- START Sunday Checkbox Input --}}
                            {!!
                                Form::checkbox('sun', '1', (bool)$classRoomClass->sun , ['id' => 'sun']);
                            !!}
                            {{-- END   Sunday Checkbox Input --}}
                            <label class="form-check-label pt-2" for="sun">&nbsp;{{ __("vcr_schedule.sunday") }}</label>
                        </div>
                    </div>

                    {{-- START Sunday From Input --}}
                    {!!
                        Form::text('sun_from', $classRoomClass->sun_from, [
                            //'required' => 'required',
                            'class' => 'form-control timepicker',
                            // Check if date matching hh:mm A Pattern

                            'placeholder' => __("sessions.Time from"),
                            'autocomplete' => "off"
                        ])
                    !!}
                    {{-- END   Sunday From Input --}}

                    {{-- START Sunday To Input --}}
                    {!!
                        Form::text('sun_to', $classRoomClass->sun_to, [
                            //'required' => 'required',
                            'class' => 'form-control timepicker',
                            // Check if date matching hh:mm A Pattern

                            'placeholder' => __("sessions.Time to"),
                            'autocomplete' => "off"
                        ])
                    !!}
                    {{-- END   Sunday To Input --}}
                </div>
            </div>
            {{-- END   Sunday Group --}}

            {{-- START Monday Group --}}
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon" style="width: 120px">
                        <div class="input-group-text">
                            {{-- START Monday Checkbox Input --}}
                            {!!
                                Form::checkbox('mon', '1', (bool)$classRoomClass->mon , ['id' => 'mon']);
                            !!}
                            {{-- END   Monday Checkbox Input --}}
                            <label class="form-check-label pt-2" for="mon">&nbsp;{{ __("vcr_schedule.monday") }}</label>
                        </div>
                    </div>

                    {{-- START Monday From Input --}}
                    {!!
                        Form::text('mon_from', $classRoomClass->mon_from, [
                            //'required' => 'required',
                            'class' => 'form-control timepicker',
                            // Check if date matching hh:mm A Pattern

                            'placeholder' => __("sessions.Time from"),
                            'autocomplete' => "off"
                        ])
                    !!}
                    {{-- END   Monday From Input --}}

                    {{-- START Monday To Input --}}
                    {!!
                        Form::text('mon_to', $classRoomClass->mon_to, [
                            //'required' => 'required',
                            'class' => 'form-control timepicker',
                            // Check if date matching hh:mm A Pattern

                            'placeholder' => __("sessions.Time to"),
                            'autocomplete' => "off"
                        ])
                    !!}
                    {{-- END   Monday To Input --}}
                </div>
            </div>
            {{-- END   Monday Group --}}

            {{-- START Tuesday Group --}}
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon" style="width: 120px">
                        <div class="input-group-text">
                            {{-- START Tuesday Checkbox Input --}}
                            {!!
                                Form::checkbox('tue', '1', (bool)$classRoomClass->tue , ['id' => 'tue']);
                            !!}
                            {{-- END   Tuesday Checkbox Input --}}
                            <label class="form-check-label pt-2" for="tue">&nbsp;{{ __("vcr_schedule.tuesday") }}</label>
                        </div>
                    </div>

                    {{-- START Tuesday From Input --}}
                    {!!
                        Form::text('tue_from', $classRoomClass->tue_from, [
                            //'required' => 'required',
                            'class' => 'form-control timepicker',
                            // Check if date matching hh:mm A Pattern

                            'placeholder' => __("sessions.Time from"),
                            'autocomplete' => "off"
                        ])
                    !!}
                    {{-- END   Tuesday From Input --}}

                    {{-- START Tuesday To Input --}}
                    {!!
                        Form::text('tue_to', $classRoomClass->tue_to, [
                            //'required' => 'required',
                            'class' => 'form-control timepicker',
                            // Check if date matching hh:mm A Pattern

                            'placeholder' => __("sessions.Time to"),
                            'autocomplete' => "off"
                        ])
                    !!}
                    {{-- END   Tuesday To Input --}}
                </div>
            </div>
            {{-- END   Tuesday Group --}}

            {{-- START Wednesday Group --}}
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon" style="width: 120px">
                        <div class="input-group-text">
                            {{-- START Wednesday Checkbox Input --}}
                            {!!
                                Form::checkbox('wed', '1', (bool)$classRoomClass->wed , ['id' => 'wed']);
                            !!}
                            {{-- END   Wednesday Checkbox Input --}}
                            <label class="form-check-label pt-2" for="wed">&nbsp;{{ __("vcr_schedule.wednesday") }}</label>
                        </div>
                    </div>

                    {{-- START Wednesday From Input --}}
                    {!!
                        Form::text('wed_from', $classRoomClass->wed_from, [
                            //'required' => 'required',
                            'class' => 'form-control timepicker',
                            // Check if date matching hh:mm A Pattern

                            'placeholder' => __("sessions.Time from"),
                            'autocomplete' => "off"
                        ])
                    !!}
                    {{-- END   Wednesday From Input --}}

                    {{-- START Wednesday To Input --}}
                    {!!
                        Form::text('wed_to', $classRoomClass->wed_to, [
                            //'required' => 'required',
                            'class' => 'form-control timepicker',
                            // Check if date matching hh:mm A Pattern

                            'placeholder' => __("sessions.Time to"),
                            'autocomplete' => "off"
                        ])
                    !!}
                    {{-- END   Wednesday To Input --}}
                </div>
            </div>
            {{-- END   Wednesday Group --}}

            {{-- START Thursday Group --}}
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon" style="width: 120px">
                        <div class="input-group-text">
                            {{-- START Thursday Checkbox Input --}}
                            {!!
                                Form::checkbox('thu', '1', (bool)$classRoomClass->thu , ['id' => 'thu']);
                            !!}
                            {{-- END   Thursday Checkbox Input --}}
                            <label class="form-check-label pt-2 pl-1 @if('rtl') pr-1 @endif" for="thu"> {{ __("vcr_schedule.thursday") }}</label>
                        </div>
                    </div>

                    {{-- START Thursday From Input --}}
                    {!!
                        Form::text('thu_from', $classRoomClass->thu_from, [
                            //'required' => 'required',
                            'class' => 'form-control timepicker',
                            // Check if date matching hh:mm A Pattern

                            'placeholder' => __("sessions.Time from"),
                            'autocomplete' => "off"
                        ])
                    !!}
                    {{-- END   Thursday From Input --}}

                    {{-- START Thursday To Input --}}
                    {!!
                        Form::text('thu_to', $classRoomClass->thu_to, [
                            //'required' => 'required',
                            'class' => 'form-control timepicker',
                            // Check if date matching hh:mm A Pattern

                            'placeholder' => __("sessions.Time to"),
                            'autocomplete' => "off"
                       ])
                    !!}
                    {{-- END   Thursday To Input --}}
                </div>
            </div>
            {{-- END   Thursday Group --}}

            {{-- START Friday Group --}}
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon" style="width: 120px">
                        <div class="input-group-text">
                            {{-- START Friday Checkbox Input --}}
                            {!!
                                Form::checkbox('fri', '1', (bool)$classRoomClass->fri , ['id' => 'fri']);
                            !!}
                            {{-- END   Friday Checkbox Input --}}
                            <label class="form-check-label pt-2 pl-1 @if('rtl') pr-1 @endif" for="fri"> {{ __("vcr_schedule.friday") }}</label>
                        </div>
                    </div>

                    {{-- START Friday From Input --}}
                    {!!
                        Form::text('fri_from', $classRoomClass->fri_from, [
                            //'required' => 'required',
                            'class' => 'form-control timepicker',
                            // Check if date matching hh:mm A Pattern

                            'placeholder' => __("sessions.Time from"),
                            'autocomplete' => "off"
                        ])
                    !!}
                    {{-- END   Friday From Input --}}

                    {{-- START Friday To Input --}}
                    {!!
                     Form::text('fri_to', $classRoomClass->fri_to, [
                            //'required' => 'required',
                            'class' => 'form-control timepicker',
                            // Check if date matching hh:mm A Pattern

                            'placeholder' => __("sessions.Time to"),
                            'autocomplete' => "off"
                            ])
                    !!}
                    {{-- END   Friday To Input --}}
                </div>
            </div>
            {{-- END   Friday Group --}}

            {{-- START Saturday Group --}}
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon" style="width: 120px">
                        <div class="input-group-text">
                            {{-- START Saturday Checkbox Input --}}
                            {!!
                                Form::checkbox('sat', '1', (bool)$classRoomClass->sat , ['id' => 'sat']);
                            !!}
                            {{-- END   Saturday Checkbox Input --}}
                            <label class="form-check-label pt-2" for="sat">&nbsp;{{ __("vcr_schedule.saturday") }}</label>
                        </div>
                    </div>

                    {{-- START Saturday From Input --}}
                    {!!
                        Form::text('sat_from', $classRoomClass->sat_from, [
                            //'required' => 'required',
                            'class' => 'form-control timepicker',
                            // Check if date matching hh:mm A Pattern

                            'placeholder' => __("sessions.Time from"),
                            'autocomplete' => "off"
                        ])
                    !!}
                    {{-- END   Saturday From Input --}}

                    {{-- START Saturday To Input --}}
                    {!!
                        Form::text('sat_to', $classRoomClass->sat_to, [
                            //'required' => 'required',
                            'class' => 'form-control timepicker',
                            // Check if date matching hh:mm A Pattern

                            'placeholder' => __("sessions.Time to"),
                            'autocomplete' => "off"
                        ])
                    !!}
                    {{-- END   Saturday To Input --}}
                </div>
            </div>
            {{-- END   Saturday Group --}}
        </div>

    </div>

</div>
{{-- Program, From To Fields END --}}

{{-- START Form Buttons --}}
<div class="form-group">
    <button type="submit" class="btn btn-success">{{ __("subject.Save") }}</button>
</div>
{{-- END   Form Buttons --}}

@push('scripts')
    <script>
        $("#subject_id").change(function () {
            $('#instructor_id').empty();
            $.get('{{ route('school-branch-supervisor.classrooms.classroomClasses.ajax.getSubjectInstructors', ["branch" => $branch ?? null]) }}',
            {
                '_token': $('meta[name=csrf-token]').attr('content'),
                'subject_id': $(this).val(),
            })
            .done(function (response) {
                $.each(response.instructors, function (i, item) {
                    $('#instructor_id').append(`<option value="${i}">${item}</option>`);
                });
            });
        });
        @if(request()->old('subject_id'))
        $('#subject_id').trigger('change');
        @endif

        $(".repeat_by").on('change', function () {
            console.log($(this).val());
            if ($(this).val() === '{{ App\OurEdu\SchoolAccounts\Enums\ClassroomClassEnum::HOURLY }}') {
                $("#repetition_days").show();
                $("#repetition_days .timepicker").show();
                $("#until_date").show();
                $("#repetition_times").show();
            } else if($(this).val() === '{{ App\OurEdu\SchoolAccounts\Enums\ClassroomClassEnum::NOREPEAT }}') {
                $("#repetition_days").hide();
                $("#repetition_days .timepicker").hide();
                $("#until_date").hide();
                $("#repetition_times").hide();
            } else if($(this).val() === '{{ App\OurEdu\SchoolAccounts\Enums\ClassroomClassEnum::WEEKLY }}'){
                $("#repetition_days").show();
                $("#repetition_days .timepicker").hide();
                $("#until_date").show();
                $("#repetition_times").show();
            }else{
                $("#repetition_days").hide();
                $("#repetition_days .timepicker").hide();
                $("#until_date").show();
                $("#repetition_times").show();
            }

            // $("#all_day").trigger("change");
        });
        $(".repeat_by").trigger("change");
    </script>

@endpush
