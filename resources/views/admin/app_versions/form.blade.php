@foreach($rows as $row)
    @include('form.input',['type'=>'text','name'=>$row->id,'value'=>$row->version,'attributes'=>['class'=>'form-control ','label'=>$row->type]])
@endforeach

@push('css')
    <style>
        #googleMap {
            position: fixed !important;
            height: 100% !important;
            width: 100% !important;
        }
    </style>
@endpush
