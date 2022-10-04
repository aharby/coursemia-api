<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            {!! Form::open(['method' => 'get']) !!}
            <div class="row">
                <div class=" form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <div class="input-group-text h-100">
                                {{ __("app.School Account Branches") }}
                            </div>
                        </div>

                        {!! Form::select('branch', $branches , request()->get('branch'), ['class' => 'form-control' , 'placeholder' => trans('app.School Account Branches') , 'style' => 'height: 55px;']) !!}
                    </div>
                </div>
                <div class="col-1"></div>

                <div class=" form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <div class="input-group-text h-100">
                                {{ trans("reports.from") }}
                            </div>
                        </div>
                        <input type="text" name="from" id="from_date" value="{{ old("from", request()->get("from")) }}" class="form-control">
                    </div>
                </div>
                <div class="col-1"></div>

                <div class=" form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <div class="input-group-text h-100">
                                {{ __("reports.to") }}
                            </div>
                        </div>
                        <input type="text" name="to" id="to_date" value="{{ old("to", request()->get("to")) }}" class="form-control">
                    </div>
                </div>

                <div class="col-md-12 form-group">
                    {{--                    <a href="" class="btn btn-md btn-danger "><i class="mdi mdi-delete-circle"></i> {{ trans('app.Reset') }} </a>--}}
                    <button class="btn btn-md btn-success"><i class="mdi mdi-filter"></i> {{ trans('app.Search') }} </button>
                </div>

            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
