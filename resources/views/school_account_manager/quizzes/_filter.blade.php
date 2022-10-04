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
                                    {{ __('app.School Account Branches') }}
                                </div>
                            </div>

                            {!! Form::select('branch', $branches , request()->get('branch'), ['id'=> "branchSelect", 'class' => 'form-control' , 'placeholder' => trans('app.School Account Branches') , 'style' => 'height: 55px;']) !!}

                        </div>
                    </div>
                </div>

                <div class="col-4">
                    <div class=" form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <div class="input-group-text h-100">
                                    {{ __('quiz.type') }}
                                </div>
                            </div>

                            {!! Form::select('quizType', $quizTypes, request()->get('quizType'), ['id'=> "quizType", 'class' => 'form-control' , 'placeholder' => trans('quiz.type') , 'style' => 'height: 55px;']) !!}

                        </div>
                    </div>
                </div>

                <div class="col-4" id="subjectsContainer">
                    <div class=" form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <div class="input-group-text h-100">
                                    {{ __('quiz.subject') }}
                                </div>
                            </div>

                            {!! Form::select('subject', $subjects??[] , request()->get('subject'), ['id'=> "subjects", 'class' => 'form-control' , 'placeholder' => trans('quiz.subject') , 'style' => 'height: 55px;']) !!}

                        </div>
                    </div>
                </div>

                <div class="col-4" id="creatorContainer">
                    <div class=" form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <div class="input-group-text h-100">
                                    {{ __('quiz.creator') }}
                                </div>
                            </div>

                            {!! Form::select('created_by', $instructors ?? [], request()->get('created_by'), ['id'=> "created_by", 'class' => 'form-control' , 'placeholder' => trans('quiz.creator') , 'style' => 'height: 55px;']) !!}

                        </div>
                    </div>
                </div>

                <div class="col-4">
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
                </div>

                <div class="col-4">
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
                </div>

                <div class="col-md-12 form-group">
                    <button class="btn btn-md btn-success"><i class="mdi mdi-filter"></i> {{ trans('app.Search') }} </button>
                </div>

            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
