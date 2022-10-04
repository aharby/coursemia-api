<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            {!! Form::open(['method' => 'get']) !!}
            <div class="row">

                <div class="col-md-2 form-group">
                    {!! Form::select('educational_system', $educational_systems , request()->get('educational_system'), ['class' => 'form-control' , 'placeholder' => trans('app.Educational Systems') , 'style' => 'height: 55px;']) !!}
                </div>
                <div class="col-md-2 form-group">
                    {!! Form::select('grade_class', $grade_classes , request()->get('grade_class'), ['class' => 'form-control' , 'placeholder' => trans('app.Grade Classes') , 'style' => 'height: 55px;']) !!}
                </div>
                <div class="col-md-12 form-group">
                    <button class="btn btn-md btn-success"><i class="mdi mdi-filter"></i> {{ trans('app.Search') }} </button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
