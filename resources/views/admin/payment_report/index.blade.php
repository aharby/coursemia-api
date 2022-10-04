@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)



@section('content')
<div class="row">
    
    <div class="row">
        <div class="col-md-6 mt-3" style="margin-top: 20px;">
            <div class=" form-group">
                <div class="input-group">
                    <div class="input-group-addon">
                        <div class="input-group-text h-100">
                            {{ __("payments.total_students") }}
                        </div>
                    </div>
                    <input type="text" disabled style="height: 55px" name="to_date" id="to_date" value="{{ $total_students }}"  class="form-control" >
                </div>
            </div>
        </div>
        <div class="col-md-6 mt-3" style="margin-top: 20px;">
            <div class=" form-group">
                <div class="input-group">
                    <div class="input-group-addon">
                        <div class="input-group-text h-100">
                            {{ __("payments.total_transactions") }}
                        </div>
                    </div>
                    <input type="text" disabled style="height: 55px" name="to_date" id="to_date" value="{{ $total_transactions }}"  class="form-control" >
                </div>
            </div>
        </div>
       
    </div>
    <div class="row">
        <div class="col-md-6 mt-3" style="margin-top: 20px;">
            <div class=" form-group">
                <div class="input-group">
                    <div class="input-group-addon">
                        <div class="input-group-text h-100">
                            {{ __("payments.total_deposit") }}
                        </div>
                    </div>
                    <input type="text" disabled style="height: 55px" name="to_date" id="to_date" value="{{ $total_deposit }}"  class="form-control" >
                </div>
            </div>
        </div>
        @if((request('product_type') && request('product_type') != \App\OurEdu\Payments\Enums\PaymentEnums::ADD_MONEY_WALLET))
        <div class="col-md-6 mt-3" style="margin-top: 20px;">
            <div class=" form-group">
                <div class="input-group">
                    <div class="input-group-addon">
                        <div class="input-group-text h-100">
                            {{ __("payments.total_spent") }}
                        </div>
                    </div>
                    <input type="text" disabled style="height: 55px" name="to_date" id="to_date" value="{{ $total_spent }}"  class="form-control" >
                </div>
            </div>
        </div>
        @endif
    </div>
    @include('admin.payment_report._filter')
   
    @if(!$rows->isEmpty())
    <table class="table table-striped table-bordered dt-responsive nowrap">
        <thead>
            <tr>
                <th class="text-center">{{ trans('payments.student_name') }}</th>
                <th class="text-center">{{ trans('payments.date_time') }}</th>
                <th class="text-center">{{ trans('payments.product_type') }}</th>

                <th class="text-center">{{ trans('payments.product') }}</th>
                <th class="text-center">{{ trans('payments.payment_method') }}</th>

                <th class="text-center">{{ trans('payments.amount') }}</th>
                {{-- <th class="text-center">{{ trans('payments.transaction_type') }}</th> --}}
                <th class="text-center">{{ trans('app.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr class="text-center">
                <td>{{ $row->receiver ? $row->receiver->name : ""}}</td>
                <td>{{  $row->created_at->format('Y-m-d H:i:s') }}</td>
                <td>{{  $product_types[$row->payment_transaction_for] ?? '' }}</td>
                <td >
                    @if($row->detail && $row->detail->subscribable)
                            @if($row->payment_transaction_for == \App\OurEdu\Payments\Enums\PaymentEnums::VCR_SPOT && $row->detail->subscribable->instructor)
                                {{ $row->detail->subscribable->instructor->name ?? '' }}
                            @else
                                {{ $row->detail->subscribable->name ?? ''}}
                            @endif
                    @endif           
                </td>
                <td>{{ $row->payment_method }}</td>

                <td>{{ $row->amount }}</td>
                {{-- <td>{{ $row->payment_transaction_type }}</td> --}}
                <td>
                    <a class="btn btn-xs btn-primary" target="_blank"  href="{{ route('admin.paymentReport.getDetails',$row->id)}}" data-toggle="tooltip"
                        data-placement="top" data-title="{{ trans('app.Details') }}">
                        <i class="fa fa-eye"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @php 
    // dd();
    @endphp
    <div class="pull-right">
        {{ $rows->links() }}
    </div>
    @else
    @include('partials.noData')
    @endif
</div>
@endsection
@section('scripts')
@parent

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript">
    var courseType = <?php echo json_encode(\App\OurEdu\Payments\Enums\PaymentEnums::COURSE); ?>;
    var selectAll = <?php echo json_encode(trans('payments.all')); ?>;

    $(document).ready(function () {
        let productType = $('#productTypeSelect').val();
        if(productType != "")
        {
            getProducts(productType)
        }
       
        if(productType == courseType)
        {
            $('#InstructorCourse').css('display','block');
        }else{
            $('#InstructorCourse').css('display','none');
        }
        $('#datePickFrom').datepicker({
            dateFormat: 'yy-mm-dd'
        });
        $('#datePickTo').datepicker({
            dateFormat: 'yy-mm-dd',
        });
    });

    function replaceUrlParam(url, paramName, paramValue) {
        if (paramValue == null) {
            paramValue = '';
        }
        var pattern = new RegExp('\\b(' + paramName + '=).*?(&|#|$)');
        if (url.search(pattern) >= 0) {
            var newParams = url.replace(pattern, '$1' + paramValue + '$2');
            history.pushState({}, null, window.location.href.split('?')[0] + newParams);
        }
    }

    $(document).on('change','#productTypeSelect',function(){
        let productType = $(this).val();
        $('#productId').empty().append("<option value=''>"+selectAll+"</option>")
        $('#courseId').empty().append("<option value=''>"+selectAll+"</option>")

        toggleInstructorCourse(productType,courseType);
        if(productType != "")
        {
            replaceUrlParam(window.location.search,"product_id");
            getProducts(productType)
        }
       
    });

    function toggleInstructorCourse(productType,courseType,productId = null){
        if(productType == courseType)
        {
            $('#InstructorCourse').css('display','block');
            if(productId != "" || productId != null)
                getCourses(productType,productId);
        }else{
            $('#InstructorCourse').css('display','none');
        }
    }
    $(document).on('change','#productId',function(){
        let productType = $('#productTypeSelect').val();
        let productId = $(this).val();
        toggleInstructorCourse(productType,courseType,productId);
    });

    function getCourses(productType,productId)
    {
        $.get('{{ route('admin.paymentReport.getProducts') }}',
        {
            'product_type':productType,
            'product_id':productId
        })
        .done(function (response) {
            let courseId = new URL(location.href).searchParams.get("course_id");
            if(response.courses)
            {
                $.each(response.courses, function (i, item) {
                if(courseId != "" && i == courseId)
                {
                    $('#courseId').append(`<option value="${i}" selected>${item} </option>`);
                }else{
                    $('#courseId').append(`<option value="${i}">${item} </option>`);
                }
            });

            }
        });
    }

    function getProducts(productType)
    {
        $.get('{{ route('admin.paymentReport.getProducts') }}',
        {
            'product_type':productType,
            // '_token': $('meta[name=csrf-token]').attr('content'),
        })
        .done(function (response) {
            let productId = new URL(location.href).searchParams.get("product_id");;
            $.each(response.products, function (i, item) {
                if(productId != "" && i == productId)
                {
                    $('#productId').append(`<option value="${i}" selected>${item} </option>`);
                    getCourses(productType,productId)
                }else{
                    $('#productId').append(`<option value="${i}">${item} </option>`);
                }
            });
        });
    }
</script>
@endsection
