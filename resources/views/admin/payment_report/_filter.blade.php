<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            {!! Form::open(['method' => 'get']) !!}
            <div class="row">
               
                <div class="col-md-6">
                    <div class=" form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <div class="input-group-text h-100">
                                    {{ __("reports.from") }}
                                </div>
                            </div>
                            <input type="text" autocomplete="off" style="height: 55px" name="from_date" id="datePickFrom" value="{{ old("from_date", request()->get("from_date")) }}" placeholder="{{ __("quiz.date") }}" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class=" form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <div class="input-group-text h-100">
                                    {{ __("reports.to") }}
                                </div>
                            </div>
                            <input type="text" autocomplete="off" style="height: 55px" name="to_date" id="datePickTo" value="{{ old("to_date", request()->get("to_date")) }}" placeholder="{{ __("quiz.date") }}" class="form-control " >
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class=" form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <div class="input-group-text h-100">
                                    {{ __('payments.product_type') }}
                                </div>
                            </div>

                            {!! Form::select('product_type', $product_types , request()->get('product_type'), ['id'=> "productTypeSelect", 'class' => 'form-control' , 'placeholder' => trans('payments.all') , 'style' => 'height: 55px;']) !!}

                        </div>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class=" form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <div class="input-group-text h-100">
                                    {{ __('payments.product') }}
                                </div>
                            </div>

                            {!! Form::select('product_id', [], request()->get('product_id'), ['id'=> "productId", 'class' => 'form-control' , 'placeholder' => trans('payments.all') , 'style' => 'height: 55px;']) !!}

                        </div>
                    </div>
                </div>
            </div>
                

            <div class="row">
                <div class="col-md-6" id="InstructorCourse" style="display:none">
                    <div class=" form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <div class="input-group-text h-100">
                                    {{ __('payments.course') }}
                                </div>
                            </div>

                            {!! Form::select('course_id', [], request()->get('course_id'), ['id'=> "courseId", 'class' => 'form-control' , 'placeholder' => trans('payments.all') , 'style' => 'height: 55px;']) !!}

                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class=" form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <div class="input-group-text h-100">
                                    {{ __('payments.payment_method') }}
                                </div>
                            </div>

                            {!! Form::select('payment_method', $payment_methods, request()->get('payment_method'), ['id'=> "paymentMethod", 'class' => 'form-control' , 'placeholder' => trans('payments.all') , 'style' => 'height: 55px;']) !!}

                        </div>
                    </div>
                </div>
            </div>


                <div class="col-md-12 form-group">
                    <button class="btn btn-md btn-success"><i class="mdi mdi-filter"></i> {{ trans('app.Search') }} </button>
                    <a class="btn btn-md btn-success" href="{{url()->current()}}" role="button"><i class="mdi mdi-delete-circle"></i> {{ trans('app.reset') }}</a>
                    <a  class="btn btn-md btn-primary float-right" href="{{  route('admin.paymentReport.index.exports',request()->query())}}">{{ trans('app.Export')}}</a>
                </div>

            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
