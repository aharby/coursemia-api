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
                                {{ __('quiz.type') }}
                            </div>
                        </div>

                        {!! Form::select('quizType', \App\OurEdu\Quizzes\Enums\QuizTypesEnum::getAllQuizTypes() , request()->get('quizType'), ['id'=> "quizType", 'class' => 'form-control' , 'placeholder' => trans('quiz.type') , 'style' => 'height: 55px;']) !!}

                    </div>
                </div>
                </div>

                <div class="col-4">
                    <div class=" form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <div class="input-group-text h-100">
                                {{ __("quiz.grade") }}
                            </div>
                        </div>

                        <select id="gradeClass_id" class="form-control" style="height: 55px;" name="gradeClass">
                            <option selected="selected" value="">{{ __("quiz.grade") }}</option>
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
                                {{ trans('quiz.classroom') }}
                            </div>
                        </div>

                        {!! Form::select('classroom', $classrooms , request()->get('classroom'), ['id' => "classroom_id", 'class' => 'form-control', 'placeholder' => trans('quiz.classroom'), 'style' => 'height: 55px;']) !!}


                    </div>
                </div>
                </div>

                <div class="col-4">
                    <div class=" form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <div class="input-group-text h-100">
                                {{ __("quiz.instructor") }}
                            </div>
                        </div>

                        {!! Form::select('instructor', $instructors , request()->get('instructor'), ['id' => "instructor_id", 'class' => 'form-control', 'placeholder' => trans('quiz.instructor'), 'style' => 'height: 55px;']) !!}


                    </div>
                </div>
                </div>

                <div class="col-4">
                    <div class=" form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <div class="input-group-text h-100">
                                {{ __("quiz.session") }}
                            </div>
                        </div>

                        {!! Form::select('session', $sessions , request()->get('session'), ['id' => "session_id", 'class' => 'form-control', 'placeholder' => __("quiz.session"), 'style' => 'height: 55px;']) !!}

                    </div>
                </div>
                </div>


                <div class="col-4">
                    <div class=" form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <div class="input-group-text h-100">
                                {{ __("quiz.date") }}
                            </div>
                        </div>
                        <input type="text" name="date" id="date" value="{{ old("date", request()->get("date")) }}" placeholder="{{ __("quiz.date") }}" class="form-control">
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
