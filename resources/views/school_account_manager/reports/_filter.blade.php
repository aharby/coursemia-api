<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            {!! Form::open(['method' => 'get']) !!}
            <div class="row">
                <div class=" form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <div class="input-group-text h-100">
                                {{ __('school-account-branches.branch name') }}
                            </div>
                        </div>

                        {!! Form::select('branch', $schoolBranches , request()->get('branch'), ['id'=>'classroom','class' => 'form-control' , 'placeholder' => trans('school-account-branches.branch name') , 'style' => 'height: 55px;']) !!}
                    </div>
                </div>
                <div class="col-1"></div>

                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <div class="input-group-text h-100">
                                {{ __("school-account-branches.User Type") }}
                            </div>
                        </div>

                        {!! Form::select('type', $filterableTypes , request()->get('type'), ['id'=>'Types','class' => 'form-control' , 'placeholder' => trans('school-account-branches.User Type') , 'style' => 'height: 55px;']) !!}
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
                        <input type="text" name="from" id="from_date" value="{{ old("from", request()->get("from")) }}" class="form-control" autocomplete="off">
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
                        <input type="text" name="to" id="to_date" value="{{ old("to", request()->get("to")) }}" class="form-control" autocomplete="off">
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
