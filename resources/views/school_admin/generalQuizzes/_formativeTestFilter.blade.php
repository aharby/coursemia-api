<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            {!! Form::open(['method' => 'get']) !!}
            <input name="quiz_type" type="hidden" value="formative_test">
            <div class="row">
                <div class="col-4">
                    <div class=" form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <div class="input-group-text h-100">
                                    {{ __('app.School Accounts') }}
                                </div>
                            </div>

                            {!! Form::select('school_id', $schools , request()->get('school_id'), ['id'=> "schoolSelect", 'class' => 'form-control' , 'placeholder' => trans('app.School Accounts') , 'style' => 'height: 55px;']) !!}

                        </div>
                    </div>
                </div>

                <div class="col-4">
                    <div class=" form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <div class="input-group-text h-100">
                                    {{ __('app.School Account Branches') }}
                                </div>
                            </div>

                            {!! Form::select('branch_id', $branches , request()->get('branch_id'), ['id'=> "branchSelect", 'class' => 'form-control' , 'placeholder' => trans('app.School Account Branches') , 'style' => 'height: 55px;']) !!}

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
                            <input type="text" autocomplete="off" name="from_date" id="from_date" value="{{ old("from_date", request()->get("from_date")) }}" placeholder="{{ __("quiz.date") }}" class="form-control">
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
                            <input type="text" autocomplete="off" name="to_date" id="to_date" value="{{ old("to_date", request()->get("to_date")) }}" placeholder="{{ __("quiz.date") }}" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="col-md-12 form-group">
                    <button class="btn btn-md btn-success"><i class="mdi mdi-filter"></i> {{ trans('app.Search') }} </button>
                    <a class="btn btn-md btn-success" href="{{url()->current()."?quiz_type=formative_test"}}" role="button"><i class="mdi mdi-delete-circle"></i> {{ trans('app.reset') }}</a>
                </div>

            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
