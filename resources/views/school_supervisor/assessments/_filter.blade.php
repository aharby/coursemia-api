<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            {!! Form::open(['method' => 'get']) !!}
            <div class="row">
                <div class="col-4">
                    <div class=" form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <div class="input-group-text h-100">
                                    {{ __('assessment.assessor_type') }}
                                </div>
                            </div>

                            {!! Form::select('assessor_type', $assessorTypes, request()->get('assessor_type'), ['id'=> "assessorType", 'class' => 'form-control' , 'placeholder' => trans('assessment.assessor_type') , 'style' => 'height: 55px;']) !!}

                        </div>
                    </div>
                </div>



                <div class="col-4">
                    <div class=" form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <div class="input-group-text h-100">
                                    {{ __("reports.from") }}
                                </div>
                            </div>
                            <input type="text" autocomplete="off" name="from_date" id="from_date" value="{{ old("from_date", request()->get("from_date")) }}" placeholder="{{ __("quiz.date") }}" class="form-control timepicker">
                        </div>
                    </div>
                </div>

                <div class="col-4">
                    <div class=" form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <div class="input-group-text h-100">
                                    {{ __("reports.to") }}
                                </div>
                            </div>
                            <input type="text" autocomplete="off" name="to_date" id="to_date" value="{{ old("to_date", request()->get("to_date")) }}" placeholder="{{ __("quiz.date") }}" class="form-control timepicker">
                        </div>
                    </div>
                </div>

                <div class="col-md-12 form-group">
                    <button class="btn btn-md btn-success"><i class="mdi mdi-filter"></i> {{ trans('app.Search') }} </button>
                </div>

            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
