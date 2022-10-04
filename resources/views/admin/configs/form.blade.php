@foreach($rows as $key=>$value)
<h4>{{$key}}</h4>
    @foreach($value as $row)
            @if($row->field_type=='file')
                @include('form.file',['name'=>'input_'.$row->id,'attributes'=>['class'=>'form-control custom-file-input','label'=>$row->label,'value'=>$row->value]])
            @elseif($row->field == 'longitude')
                @php
                    $lng = $row->value
                @endphp
                @include('form.input',['type'=>$row->field_type,'name'=>'input_'.$row->id,'value'=>$row->value,'attributes'=>['class'=>'form-control '.$row->field_class,'label'=>$row->label,$row->field]])
            @elseif($row->field == 'latitude')
                @php
                    $lat = $row->value
                @endphp
                @include('form.input',['type'=>$row->field_type,'name'=>'input_'.$row->id,'value'=>$row->value,'attributes'=>['class'=>'form-control '.$row->field_class,'label'=>$row->label,$row->field]])
            @else
                @include('form.input',['type'=>$row->field_type,'name'=>'input_'.$row->id,'value'=>$row->value,'attributes'=>['class'=>'form-control '.$row->field_class,'label'=>$row->label,$row->field]])
            @endif
    @endforeach
@endforeach
    <div class="row mg-t-20 form-group">
        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name" ></label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="z-depth-1-half map-container" style="height: 500px">
                <div id="googleMap"></div>
            </div>
        </div>
    </div>
@push('css')
    <style>
        #googleMap {
            position: fixed !important;
            height: 100% !important;
            width: 100% !important;
        }
    </style>
@endpush

@push('js')

    <script>
        $("#googleMap").style.cssText("position","fixed !important");
        var map;
        function initMap() {
            map = new google.maps.Map(document.getElementById('googleMap'), {
                center: {lat: $lat, lng: $lng},
            });
        }
    </script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDu76BJkxp3swOpvw4K6hXkLhK3CennBx4
        &callback=initMap">
    </script>
@endpush
