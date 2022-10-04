@include('form.select',['name'=>'subject_id','options'=> $subjects , $row->subject_id ?? null ,
'attributes'=>['id'=>'subject_id','class'=>'form-control','required'=>'required','label'=>trans('vcr_schedule.Subject'),'placeholder'=>trans('vcr_schedule.Subject')]])

@include('form.select',['name'=>'instructor_id','options'=> $instructors , $row->instructor_id ?? null ,
'attributes'=>['id'=>'instructor_id','class'=>'form-control','required'=>'required','label'=>trans('vcr_schedule.Instructor'),'placeholder'=>trans('vcr_schedule.Instructor')]])

@include('form.input',['type'=>'text','name'=>'from_date','value'=> $row->from_date ?? null,
'attributes'=>['class'=>'form-control nowdatepicker','label'=>trans('vcr_schedule.From Date'),'placeholder'=>trans('vcr_schedule.From Date')]])

@include('form.input',['type'=>'text','name'=>'to_date','value'=> $row->to_date ?? null,
'attributes'=>['class'=>'form-control nowdatepicker','label'=>trans('vcr_schedule.To Date'),'placeholder'=>trans('vcr_schedule.To Date')]])

@include('form.input',['type'=>'text','name'=>'price','value'=> $row->price ?? null,
'attributes'=>['class'=>'form-control','label'=>trans('vcr_schedule.Price'),'placeholder'=>trans('vcr_schedule.Price')]])

<div class="row mg-t-20">
    <label class="col-sm-2 form-control-label">{{ trans('vcr_schedule.Working Days') }}</label>
    <div class="col-sm-10 mg-t-10 mg-sm-t-0 form-check form-check-inline">

        @foreach(App\OurEdu\VCRSchedules\DayEnums::weekDays() as $day)
            @php
                $workingDay = isset($row) ? $row->workingDays()->where('day' , $day)->first() : null;
            @endphp
            @include('form.daysCheckboxes',[
                'name'=>'working_days'.'['.$day.']',
                'error_name' => 'working_days.'.$day,
                'checkedArr' => isset($row) && isset($workingDay) ? [$workingDay->day] : [],
                'from_time' => isset($workingDay) ? $workingDay->from_time : null ,
                'to_time' => isset($workingDay) ? $workingDay->to_time : null,
                'day' => $day
             ])
        @endforeach
    </div>
</div>

@php
    $attributes=['class'=>'form-control','label'=>trans('subjects.Is active')];
@endphp

@include('form.boolean',['name'=>'is_active', $attributes])


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

            if($('input[name=from_date]').val() !== '' &&  $('input[name=to_date]') !== ''){
                getDayes ($('input[name=from_date]').val() , $('input[name=to_date]').val());
               }

            $('.nowdatepicker').on('apply.daterangepicker', function(ev, picker) {
               if($('input[name=from_date]').val() !== '' &&  $('input[name=to_date]') !== ''){
                getDayes ($('input[name=from_date]').val() , $('input[name=to_date]').val());
               }

              });

            function getDayes (from , to) {

            var url = '{{ url("vcr_schedules/get/working_dayes/:from/:to") }}';
            url = url.replace(':from', from );
            url = url.replace(':to', to );

            $.get( url , {
                '_token': $('meta[name=csrf-token]').attr('content'),
            })
            .done(function(response){
              let data = response;
             $('.day').each(function(){
                 if( $.inArray($(this).attr('id'), data.dayes) >= 0 ){
                    $(this).show();
                 }else{
                    $(this).hide();
                    if( $(this).children('input[type=checkbox]').is(":checked")){
                        $(this).children('input[type=checkbox]').prop('checked',0);
                    }

                 }

              });


            });
        }

        });
    </script>
@endpush

