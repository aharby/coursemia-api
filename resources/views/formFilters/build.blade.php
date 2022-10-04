@if(isset($filters))
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 x_panel">
            {{ Form::open([ 'url' => url()->current() , 'method' => 'get','class'=>'form-vertical']) }}
                @foreach($filters as $filter)
                    @php
                        if(!array_key_exists('attributes' , $filter)) {
                            $filter['attributes'] = [];
                        }
                    @endphp
                    @include('formFilters.' . $filter['type'], $filter)
                @endforeach
                <div class="form-group">
                    <div class="col-md-2 col-sm-2 col-xs-2">
                        <button type="submit" class="btn btn-success"> <i class="fa fa-search"></i></button>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
@endif
