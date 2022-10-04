<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            {!! Form::open(['method' => 'get']) !!}
            <div class="row">

                <div class="col-md-3">
                    <div class=" form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <div class="input-group-text h-100">
                                    {{ __("reports.from") }}
                                </div>
                            </div>
                            <input type="text" autocomplete="off" style="height: 55px;width: 100%;" name="from_date" id="datePickFrom" class="datepicker" value="{{ old("from_date", request()->get("from_date")) }}" placeholder="{{ __("quiz.date") }}" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class=" form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <div class="input-group-text h-100">
                                    {{ __("reports.to") }}
                                </div>
                            </div>
                            <input type="text" autocomplete="off" style="height: 55px;width: 100%;" name="to_date" id="datePickTo" class="datepicker" value="{{ old("to_date", request()->get("to_date")) }}" placeholder="{{ __("quiz.date") }}" class="form-control " >
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-md-6 form-group">
                <button class="btn btn-md btn-success"><i class="mdi mdi-filter"></i> {{ trans('app.Search') }} </button>
                <a class="btn btn-md btn-success" href="{{url()->current()}}" role="button"><i class="mdi mdi-delete-circle"></i> {{ trans('app.reset') }}</a>
            </div>

        </div>
        {!! Form::close() !!}
    </div>
</div>
</div>
