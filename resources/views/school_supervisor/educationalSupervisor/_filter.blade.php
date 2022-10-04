<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            {!! Form::open(['method' => 'get']) !!}
            <div class="row">
                @if($username)
                <div class="col-md-2 form-group">
                    {!! Form::text('username', request()->get('username'), ['class' => 'form-control' , 'placeholder' => trans('filters.id')]) !!}
                </div>
                @endif
                <div class="col-md-2 form-group">
                    {!! Form::select('educational_system', $educational_systems , request()->get('educational_system'), ['class' => 'form-control' , 'placeholder' => trans('educationalSupervisor.educational system') , 'style' => 'height: 55px;']) !!}
                </div>
                <div class="col-md-2 form-group">
                    {!! Form::select('grade_class', $grade_classes , request()->get('grade_class'), ['class' => 'form-control' , 'placeholder' => trans('educationalSupervisor.grade class') , 'style' => 'height: 55px;']) !!}
                </div>
                <div class="col-md-12 form-group">
                    <a href="" class="btn btn-md btn-danger "><i class="mdi mdi-delete-circle"></i> {{ trans('app.Reset') }} </a>
                    <button class="btn btn-md btn-success"><i class="mdi mdi-filter"></i> {{ trans('app.Search') }} </button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
