
    <div class="day" id="{{ $day }}">
        {!! Form::checkbox($name.'[day]', $day, in_array($day, $checkedArr)) !!}
        <label class="form-check-label">  {{ trans('vcr_schedule.'.$day) }}  </label>
        <div class="operationTime" style="display: none">
            <input type="text" name="{{ $name.'[from_time]' }}"
                   value="{{ $from_time }}"
                   class="timepicker1"
                   placeholder="{{ trans('vcr_schedule.From Time') }}"/>
            <input type="text" name="{{ $name.'[to_time]' }}"
                   value="{{ $to_time }}"
                   class="timepicker1"
                   placeholder="{{ trans('vcr_schedule.To Time') }}"/>
            <div>
                @if(@$errors)
                    @foreach($errors->get($error_name.".from_time") as  $message)
                        <span class='help-inline text-danger'>{{$message}}</span><br>
                    @endforeach
                    @foreach($errors->get($error_name.".to_time") as  $message)
                        <span class='help-inline text-danger'>{{$message}}</span>
                    @endforeach
                @endif
            </div>
        </div>


    </div>

@push('js')
    <script>
        $(document).ready(function(){
            $('input[type=checkbox]').change(function(){
                if (this.checked) {
                    // if checked show the time
                    $(this).parent().children('.operationTime').show();
                }else{
                    // if not checked hide the time
                    $(this).parent().children('.operationTime').hide();
                }
            });
            $('input[type=checkbox]').trigger('change');

            // timepicker
            $(function() {
                $('.timepicker1').timepicker({
                    timeFormat: 'h:i A',
                });
            });
        });
    </script>
@endpush

