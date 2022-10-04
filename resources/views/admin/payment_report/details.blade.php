<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet" />
    @if(app()->getLocale() == 'ar')
    {{ Html::style(mix('assets/admin-rtl/css/admin.css')) }}
    @else
    {{ Html::style(mix('assets/admin/css/admin.css')) }}
    @endif
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
        integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    {{ Html::style('https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css') }}
    {{ Html::style('https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.css') }}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js"></script>


    <title></title>
    <style>
        /* http://meyerweb.com/eric/tools/css/reset/
     v2.0 | 20110126
     License: none (public domain)
  */
        html,
        body {
            font-family: "Cairo", sans-serif !important;
        }

        html,
        body,
        div,
        span,
        applet,
        object,
        iframe,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        blockquote,
        pre,
        a,
        abbr,
        acronym,
        address,
        big,
        cite,
        code,
        del,
        dfn,
        em,
        img,
        ins,
        kbd,
        q,
        s,
        samp,
        small,
        strike,
        strong,
        sub,
        sup,
        tt,
        var,
        b,
        u,
        i,
        center,
        dl,
        dt,
        dd,
        ol,
        ul,
        li,
        fieldset,
        form,
        label,
        legend,
        table,
        caption,
        tbody,
        tfoot,
        thead,
        tr,
        th,
        td,
        article,
        aside,
        canvas,
        details,
        embed,
        figure,
        figcaption,
        footer,
        header,
        hgroup,
        menu,
        nav,
        output,
        ruby,
        section,
        summary,
        time,
        mark,
        audio,
        video {
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            font: inherit;
            vertical-align: baseline;
        }

        /* HTML5 display-role reset for older browsers */
        article,
        aside,
        details,
        figcaption,
        figure,
        footer,
        header,
        hgroup,
        menu,
        nav,
        section {
            display: block;
        }

        body {
            line-height: 1;
        }

        ol,
        ul {
            list-style: none;
        }

        blockquote,
        q {
            quotes: none;
        }

        blockquote:before,
        blockquote:after,
        q:before,
        q:after {
            content: "";
            content: none;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        /* -------------------- */
        body {
            font-family: "Cairo", sans-serif;
        }

        main {}

        nav {
            text-align: center;
            padding: 2em;
            margin-bottom: 1em;
        }

        nav img {}

        .card {
            padding: 1rem;
            border: 1px solid rgb(207, 207, 207);
            /* border-radius: 10px; */
            margin: auto;
            width: 80%;
            max-width: 500px;
            color: #3a3a3a;
            margin-bottom: 1em;
            /* box-shadow: 0px 0px 1px #c2c1c1; */
        }

        .card-title {
            margin-bottom: 2em;
            font-weight: bold;
            color: grey;
            font-size: 1.5rem;
        }

        .card-body {}

        .card-item {
            margin-bottom: 1em;
            padding: 0.5em 0;
        }

        .card-item .card-item_title {
            color: #172B4D;
            font-weight: 600;
            float: left;
            text-align: right;
        }

        .card-item .card-item_content {
            color: #7A869A;
            float: right;
            text-align: left;
            margin-top: -5px;
        }

        .welcome {
            background-color: #ebf9f2;
            padding: 1rem;
            margin: auto;
            margin-bottom: 1em;
            width: 80%;
            max-width: 500px;
        }

        .welcome h1 {
            color: #000;
            font-size: 1.5rem;
            /* margin-bottom: 1em; */
            font-weight: bold;
        }

        /* .check-wrapper {
          background-color: green;
          width: 40px;
          height: 40px;
          border-radius: 50%;
          line-height: 50px;
          margin: 0 1rem;
        }
        .check {
          display: inline-block;
          transform: rotate(45deg);
          height: 18px;
          width: 8px;
          border-bottom: 7px solid #fff;
          border-right: 7px solid #fff;
        } */
        .text-center {
            text-align: center;
        }

        .border-bottom-grey {
            border-bottom: 1px solid #c8c8c9;
            padding-bottom: 1.5em;
        }

        .flex-dir-column {
            flex-direction: column;
            align-items: flex-start;
        }

        .mb-2 {
            margin-bottom: 0.5em;
        }

        .header {
            margin-bottom: 1.5rem;
            overflow: auto;
        }

        .header .card-item_title {
            float: left;
        }

        .header .card-item_content {
            float: right;
        }

        .breakdown {
            margin-bottom: 1rem !important;
            overflow: auto;
        }

        .breakdown>div {
            color: #7a869a;
        }

        .breakdown .left {
            float: left;
        }

        .breakdown .right {
            float: right;
        }

    </style>
</head>

<body>
    <div id="content">

       
        @php
        $lang = app()->getLocale();

        @endphp
        <main>
            <nav>
                <img src="{{ asset('assets/images/logo.png') }}"
                alt="qudrat"
                    width="150px" />
            </nav>
            <div class="welcome text-center">
                <div class="check-wrapper">
                    <div class="check"></div>
                </div>
                <h1>{{__('payments.payment_invoice')}}</h1>
            </div>
            <div class="card">
                <div class="card-body">

                    <div class="card-item border-bottom-grey">
                        <div class="card-item_title" @if($lang=='en' ) style="float: left;" @else style="float: right"
                            @endif>{{ trans('payments.student_name') }}
                        </div>

                        <div class="card-item_content" @if($lang=='en' ) style="float: right;" @else style="float: left"
                            @endif> {{ $row->receiver? $row->receiver->name : ''}}</div>

                    </div>

                    {{--  --}}
                    <div class="card-item border-bottom-grey">
                        <div class="card-item_title" @if($lang=='en' ) style="float: left;" @else style="float: right"
                            @endif>{{trans('payments.date_time')}}</div>
                        <div class="card-item_content" @if($lang=='en' ) style="float: right;" @else style="float: left"
                            @endif>{{ $row->created_at }}</div>
                    </div>
                    <div class="card-item border-bottom-grey">
                        <div class="card-item_title" @if($lang=='en' ) style="float: left;" @else style="float: right"
                            @endif>{{trans('payments.product_type')}}</div>

                        <div class="card-item_content" @if($lang=='en' ) style="float: right;" @else style="float: left"
                            @endif>
                            {{ $row->payment_transaction_for ?  \App\OurEdu\Payments\Enums\PaymentEnums::getProducts()[$row->payment_transaction_for] : '' }}
                        </div>

                    </div>

                    @if($row->detail && $row->detail->subscribable)


                    @if($row->payment_transaction_for == \App\OurEdu\Payments\Enums\PaymentEnums::VCR_SPOT)
                    <div class="card-item border-bottom-grey">
                        <div class="card-item_title" @if($lang=='en' ) style="float: left;" @else style="float: right"
                            @endif>{{trans('payments.instructor')}}</div>

                        <div class="card-item_content" @if($lang=='en' ) style="float: right;" @else style="float: left"
                            @endif>
                            {{ $row->detail->subscribable->instructor->name ?? '' }}
                        </div>
                    </div>

                    <div class="card-item border-bottom-grey">
                        <div class="card-item_title" @if($lang=='en' ) style="float: left;" @else style="float: right"
                            @endif>{{trans('payments.subject')}}</div>

                        <div class="card-item_content" @if($lang=='en' ) style="float: right;" @else style="float: left"
                            @endif>
                            {{ $row->detail->subscribable->subject->name ?? '' }}
                        </div>
                    </div>

                    @if($row->detail->subscribable->vcrSession->time_to_start)
                    <div class="card-item border-bottom-grey">
                        <div class="card-item_title" @if($lang=='en' ) style="float: left;" @else style="float: right"
                            @endif>{{trans('payments.session_time')}}</div>

                        <div class="card-item_content" @if($lang=='en' ) style="float: right;" @else style="float: left"
                            @endif>
                            {{  $row->detail->subscribable->vcrSession->time_to_start }}
                        </div>
                    </div>
                    @endif
                    @elseif($row->payment_transaction_for == \App\OurEdu\Payments\Enums\PaymentEnums::COURSE)

                    <div class="card-item border-bottom-grey">
                        <div class="card-item_title" @if($lang=='en' ) style="float: left;" @else style="float: right"
                            @endif>{{trans('payments.instructor')}}</div>

                        <div class="card-item_content" @if($lang=='en' ) style="float: right;" @else style="float: left"
                            @endif>
                            {{ $row->detail->subscribable->instructor->name ?? '' }}
                        </div>
                    </div>

                    <div class="card-item border-bottom-grey">
                        <div class="card-item_title" @if($lang=='en' ) style="float: left;" @else style="float: right"
                            @endif>{{trans('payments.course_name')}}</div>

                        <div class="card-item_content" @if($lang=='en' ) style="float: right;" @else style="float: left"
                            @endif>

                            {{ $row->detail->subscribable->name ?? '' }}
                        </div>
                    </div>

                    @else
                    <div class="card-item border-bottom-grey">
                        <div class="card-item_title" @if($lang=='en' ) style="float: left;" @else style="float: right"
                            @endif>{{trans('payments.subject')}}</div>
                        <div class="card-item_content" @if($lang=='en' ) style="float: right;" @else style="float: left"
                            @endif>
                            {{ $row->detail->subscribable->name ?? ''}}
                        </div>

                    </div>

                    @endif
                    @endif

                    <div class="card-item border-bottom-grey">
                        <div class="card-item_title" @if($lang=='en' ) style="float: left;" @else style="float: right"
                            @endif>{{__('payments.payment_method')}}</div>

                        <div class="card-item_content" @if($lang=='en' ) style="float: right;" @else style="float: left"
                            @endif>
                            {{ $row->payment_method ?  \App\OurEdu\Payments\Enums\PaymentMethodsEnum::getPaymentMethods()[$row->payment_method] : '' }}
                        </div>

                    </div>

                    <div class="card-item border-bottom-grey">
                        <div class="card-item_title" @if($lang=='en' ) style="float: left;" @else style="float: right"
                            @endif>{{__('payments.amount')}}</div>
                        <div class="card-item_content" @if($lang=='en' ) style="float: right;" @else style="float: left"
                            @endif>{{$row->amount . ' '.$currencyCode }}</div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <div class="row">
        <div style="width:70px; margin:0 auto;">
            <div id="editor"></div>
            <Button id="cmd" class="btn btn-md btn-primary float-right" href="#">{{ trans('app.Export')}}</Button>

        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
    <script type="text/javascript" src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
    @php
        if($row->payment_transaction_for == \App\OurEdu\Payments\Enums\PaymentEnums::VCR_SPOT){
            $pdfName = $row->detail->subscribable->instructor->name.'__vcr_session__invoice';
        }elseif($row->payment_transaction_for == \App\OurEdu\Payments\Enums\PaymentEnums::COURSE)
        {
            $pdfName = $row->detail->subscribable->instructor->name.'-'.$row->detail->subscribable->name.'__course_invoice' ?? '';
        }elseif($row->payment_transaction_for == \App\OurEdu\Payments\Enums\PaymentEnums::SUBJECT){
            $pdfName = $row->detail->subscribable->name.'__subject_invoice';
        }else{
            $pdfName ='add_to_wallet_invoice';
        }
    @endphp
    <script>
        function CreatePDFfromHTML() {
            var pdfName = "{{ $pdfName }}";
            var HTML_Width = $("#content").width();
            var HTML_Height = $("#content").height();
            var top_left_margin = 15;
            var PDF_Width = HTML_Width + (top_left_margin * 2);
            var PDF_Height = (PDF_Width *1.5) + (top_left_margin * 2);
            var canvas_image_width = HTML_Width;
            var canvas_image_height = HTML_Height;

            var totalPDFPages = Math.ceil(HTML_Height / PDF_Height) - 1;

            html2canvas($("#content")[0]).then(function (canvas) {
                var imgData = canvas.toDataURL("image/jpeg", 1.0);
                var pdf = new jsPDF('p', 'pt', [PDF_Width, PDF_Height]);
                pdf.addImage(imgData, 'JPG', top_left_margin, top_left_margin, canvas_image_width,
                    canvas_image_height);
                for (var i = 1; i <= totalPDFPages; i++) {
                    pdf.addPage(PDF_Width, PDF_Height);
                    pdf.addImage(imgData, 'JPG', top_left_margin, -(PDF_Height * i) + (top_left_margin * 4),
                        canvas_image_width, canvas_image_height);
                }
                pdf.save(pdfName+".pdf");
                // $("#html-content").hide();
            });
        }
        $('#cmd').click(function () {
            CreatePDFfromHTML();
        });

    </script>
</body>

</html>
