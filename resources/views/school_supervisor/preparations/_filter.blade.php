<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            {!! Form::open(['method' => 'get']) !!}
            <div class="row">
                <div class=" form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <div class="input-group-text h-100">
                                {{ __("session_preparation.classroom") }}
                            </div>
                        </div>

                        {!! Form::select('classroom', $classrooms, request()->get('classroom'), ['id'=> 'classroom_id', 'class' => 'form-control', 'placeholder' => trans('session_preparation.classroom'), 'style' => 'height: 55px;']) !!}
                    </div>
                </div>
                <div class="col-1"></div>
                <div class=" form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <div class="input-group-text h-100">
                                {{ __("session_preparation.timetable") }}
                            </div>
                        </div>

                        {!! Form::select('classroomClass', $classroomClasses ?? [], request()->get('classroomClass'), ['id'=> 'classroomClass_id', 'class' => 'form-control', 'placeholder' => trans('session_preparation.timetable'), 'style' => 'height: 55px;']) !!}
                    </div>
                </div>
                <div class="col-1"></div>
                <div class=" form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <div class="input-group-text h-100">
                                {{ __("session_preparation.sessions") }}
                            </div>
                        </div>

                        {!! Form::select('classSession', $classSessions ?? [], request()->get('classSession'), ['id'=> 'classSession_id', 'class' => 'form-control', 'placeholder' => trans('session_preparation.sessions'), 'style' => 'height: 55px;']) !!}
                    </div>
                </div>
                <div class="col-1"></div>

                <div class="col-md-12 form-group">
                    {{--                    <a href="" class="btn btn-md btn-danger "><i class="mdi mdi-delete-circle"></i> {{ trans('app.Reset') }} </a>--}}
                    <button class="btn btn-md btn-success"><i class="mdi mdi-filter"></i> {{ trans('app.Search') }} </button>
                </div>

            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
