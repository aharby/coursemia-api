<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            {!! Form::open(['method' => 'get']) !!}
            <div class="row">

                <div class=" form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <div class="input-group-text h-100">
                                {{ __("reports.classroom") }}
                            </div>
                        </div>

                        {!! Form::select('classroom', $classrooms , request()->get('classroom'), ['class' => 'form-control' , 'placeholder' => trans('app.Classrooms') , 'style' => 'height: 55px;']) !!}
                    </div>
                </div>

                <div class="col-1"></div>

                <div class=" form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <div class="input-group-text h-100">
                                {{ __("reports.date") }}
                            </div>
                        </div>

                        <input type="text" name="date" id="from_time" value="{{ old("from", request()->get("date")) }}" class="form-control">
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
