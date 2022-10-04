<div class="row mg-t-20 form-group">


    <label class="control-label col-md-2 col-sm-2 col-xs-12">{{ @$attributes['label'] }} <span class="red">{{ (@$attributes['required'])?'*':'' }} {{ (@$attributes['stared'])?'*':'' }}</span></label>
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="custom-file">
            {!! Form::file($name,$attributes)!!}
            <label class="custom-file-label" for="{{ @$id }}">{{ trans('app.Choose') }}</label>
            <output id="listImage" class="listImage">
                @if(!$errors->isEmpty())
                    @foreach($errors->get($name) as $message)
                        <span class='help-inline text-danger'>{{ $message }}</span>
                    @endforeach
                    <br>
                @endif

                @if(isset($value))
                    @if(@$attributes['file_type'] == 'attachment' )
                        {!! viewFile($value) !!}
                    @else
                        {!! viewInputImage($value,'small') !!}
                    @endif
                @endif
            </output>


        </div>

    </div>
</div>
@push('js')
    <script>
        function handleFileSelect(evt) {
            var files = evt.target.files;

            // Loop through the FileList and render image files as thumbnails.
            for (var i = 0, f; f = files[i]; i++) {

                // Only process image files.
                if (!f.type.match('image.*')) {
                    continue;
                }

                var reader = new FileReader();

                // Closure to capture the file information.
                reader.onload = (function(theFile) {
                    return function(e) {
                        // Render thumbnail.
                        var span = document.createElement('span');
                        span.innerHTML =
                            [
                                '<img style="height: 300px; width:300px; border: 1px solid #000; margin: 5px" src="',
                                e.target.result,
                                '" title="', escape(theFile.name),
                                '"/>'
                            ].join('');
                        $('.listImage img').remove();
                        document.getElementById('listImage').insertBefore(span, null);
                    };
                })(f);

                // Read in the image file as a data URL.
                reader.readAsDataURL(f);
            }
        }

        document.getElementById('{{ $name }}').addEventListener('change', handleFileSelect, false);
    </script>
@endpush
