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

                            {!! Form::select('branch_id', $branches , request()->get('branch_id'), ['id'=> "branchSelect", 'class' => 'form-control' , 'placeholder' => trans('app.School Account Branches') , 'style' => 'height: 55px;']) !!}

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

                            {!! Form::select('quiz_type', $quizTypes, request()->get('quiz_type'), ['id'=> "quizType", 'class' => 'form-control' , 'placeholder' => trans('quiz.type') , 'style' => 'height: 55px;']) !!}

                        </div>
                    </div>
                </div>

                <div class="col-4">
                    <div class=" form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <div class="input-group-text h-100">
                                    {{ __("quiz.gradeClass") }}
                                </div>
                            </div>
                            <select id="gradeClass_id" class="form-control" style="height: 55px;" name="gradeClass">
                                <option selected="selected" value="">{{ __("quiz.gradeClass") }}</option>
                                @foreach($gradeClasses as $gradeClass)
                                    <option value="{{ $gradeClass->id }}" {{ request()->get("gradeClass") == $gradeClass->id ? "selected" : null  }}>{{ $gradeClass->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-4">
                    <div class=" form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <div class="input-group-text h-100">
                                    {{ __("quiz.subject") }}
                                </div>
                            </div>

                            {!! Form::select('subject', $subjects ?? [] , request()->get('subject'), ['id' => "subject_id", 'class' => 'form-control', 'placeholder' => trans('quiz.subject'), 'style' => 'height: 55px;']) !!}

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
                </div>

            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
