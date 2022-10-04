{{--<a href="{{url('students-sample.xls')}}" ><h4>{{ trans('app.Excel Sample file') }}</h4></a>--}}


@include('form.input',
[
    'type'=>'text',
    'name'=>'title',
    'value'=>  old('title', $formativeTest->title ?? null),
    'attributes'=>[
        'class'=>'form-control',
        'label'=>trans('formative_tests.name'),
        'placeholder'=>trans('formative_tests.name'),
        'required'=>'required'
    ]
])



@include('form.select',
[
    'name'=>'educational_system_id',
    'options'=> $educationalSystems ?? [],
    'value' =>  old('educational_system_id', $formativeTest->educational_system_id ?? null),
    'attributes'=>[
        'id'=>'educational_system_id',
        'class'=>'form-control',
        'required'=>'required',
        'label'=>trans('formative_tests.educational system'),
        'placeholder'=>trans('formative_tests.educational system')
    ]
])

@include('form.select',
[
    'name'=>'grade_class_id',
    'options'=> $gradeClasses ?? [],
    'value' => old('grade_class_id', $formativeTest->grade_class_id ?? null) ,
    'attributes'=>[
        'id'=>'grade_classes',
        'class'=>'form-control',
        'required'=>'required',
        'label'=>trans('formative_tests.grade classes'),
        'placeholder'=>trans('formative_tests.grade classes')
    ]
])

@include('form.select',
[
    'name'=>'subject_id',
    'options'=> $subjects ?? [],
    'value' => old('subject_id', $formativeTest->subject_id ?? null),
    'attributes'=>[
        'id'=>'subject_id',
        'class'=>'form-control',
        'required'=>'required',
        'label'=>trans('formative_tests.subjects'),
        'placeholder'=>trans('formative_tests.subjects')
    ]
])

<div class="form-group">

    {{-- START To + Time Fields --}}
    <div class="form-group">
        <div class="input-group">
            <div class="input-group-addon">
                <div class="input-group-text h-100">
                    {{ __("formative_tests.start Date") }}
                </div>
            </div>

            {!!
                Form::text('from', old('from', isset($formativeTest) ? $formativeTest->start_at->todatestring() : null), [
                    'required' => 'required',
                    'class' => 'form-control datepicker',
                    'placeholder' => __("formative_tests.start Date"),
                    'id' => 'from-datepicker',
                    'autocomplete' => "off"
                ])
            !!}

            <div class="input-group-addon" style="margin-left: 10px;">
                <div class="input-group-text h-100">
                    {{ __("formative_tests.Time From") }}
                </div>
            </div>

            {!!
                Form::text('from_time', old('from_time', isset($formativeTest) ? $formativeTest->start_at->toTimeString(): ""), [
                    'required' => 'required',
                    'class' => 'form-control timepicker timepicker-av',
                    'id'    => 'from_time',
                    // Check if date matching hh:mm A Pattern

                    'placeholder' => __("formative_tests.Time From"),
                    'autocomplete' => "off"
                ])
            !!}
        </div>
    </div>
    {{-- END   To + Time Fields --}}
</div>

<div class="form-group">

    {{-- START To + Time Fields --}}
    <div class="form-group">
        <div class="input-group">
            <div class="input-group-addon">
                <div class="input-group-text h-100">
                    {{ __("formative_tests.end Date") }}
                </div>
            </div>

            {!!
                  Form::text('to', old('to', isset($formativeTest) ? $formativeTest->end_at->todatestring() : null), [
                    'required' => 'required',
                    'class' => 'form-control datepicker',
                    'id' => 'to-datepicker',
                    'placeholder' => __("formative_tests.end Date"),
                    'autocomplete' => "off"
                ])
            !!}

            <div class="input-group-addon" style="margin-left: 10px;">
                <div class="input-group-text h-100">
                    {{ __("formative_tests.Time To") }}
                </div>
            </div>

            {!!
                Form::text('to_time', old('to_time', isset($formativeTest) ? $formativeTest->end_at->toTimeString(): ""), [
                    'required' => 'required',
                    'class' => 'form-control timepicker timepicker-av',
                    'id'    => 'to_time',
                    // Check if date matching hh:mm A Pattern

                    'placeholder' => __("formative_tests.Time To"),
                    'autocomplete' => "off"
                ])
            !!}
        </div>
    </div>
    {{-- END   To + Time Fields --}}
</div>


@include('form.input',
[
    'type'=>'number',
    'name'=>'test_time',
    'value'=> old('test_time', isset($formativeTest) ? $formativeTest->test_time / 60 : null),
    'attributes'=>[
        'class'=>'form-control',
        'label'=>trans('formative_tests.time in minutes'),
        'placeholder'=>trans('formative_tests.time in minutes'),
        'required'=>'required',
        'min' => 0,
    ]
])

<div class="form-group">
    <div class="input-group">
        <div class="input-group-addon" style="width: 188px">
            <div class="input-group-text">
                {{-- START Monday Checkbox Input --}}
                {!!
                    Form::checkbox('random_question', '1', old('random_question', $formativeTest->random_question ?? false), ['id' => 'mon']);
                !!}
                {{-- END   Monday Checkbox Input --}}
                <label class="form-check-label pt-2" for="mon">&nbsp;{{ __("formative_tests.random question") }}</label>
            </div>
        </div>
    </div>
</div>

@push('scripts')

<script>
    $(function(){
    var dtToday = new Date();
    var month = dtToday.getMonth() + 1;
    var day = dtToday.getDate();
    var year = dtToday.getFullYear();
    if(month < 10)
        month = '0' + month.toString();
    if(day < 10)
        day = '0' + day.toString();

    var minDate= year + '-' + month + '-' + day;

    $('#from-datepicker').attr('min', minDate);
});
</script>
    <script>

        let oldGradeClass = "{{old('grade_class_id', $formativeTest->grade_class_id ?? null)}}"
        let educationalSystemSelector = $("#educational_system_id");
        let oldSubjectId = "{{old('subject_id', $formativeTest->subject_id ?? null)}}"
        let gradeClassesSelector = $("#grade_classes");

        educationalSystemSelector.change(function () {
            gradeClassesSelector.empty();
            $.get('{{ route('school-admin.formative-test.ajax.get.educational.system.grade.classes') }}',
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'educational_system_id': $(this).val()
                })
                .done(function (response) {
                    $.each(response.gradeClasses, function (i, title) {
                        var isSelected = (i == oldGradeClass) ? "selected" : "";
                        gradeClassesSelector.append(`<option value="${i}" ${isSelected}>${title}</option>`);
                    });

                    gradeClassesSelector.trigger("change");
                });
        });
        educationalSystemSelector.trigger("change");

        gradeClassesSelector.change(function () {
            $('#subject_id').empty();
            $.get('{{ route('school-admin.formative-test.ajax.get.grade.classes.subjects') }}',
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'grade_class_id': $(this).val()
                })
                .done(function (response) {
                    $.each(response.subjects, function (i, name) {
                        var isSelected = (i == oldSubjectId) ? "selected" : "";

                        $('#subject_id').append(`<option value="${i}" ${isSelected}>${name}</option>`);
                    });
                });
        });
    </script>
@endpush
