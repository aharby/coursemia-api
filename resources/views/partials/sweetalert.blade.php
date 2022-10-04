@push('scripts')
    <script>
        $('.delete-confirm').on('click', function (event) {
            event.preventDefault();
            const url = $(this).attr('href');
            swal({
                title:'{!! $title !!}',
                text:'{!! isset($text) ? $text : ''!!}',
                buttons: ['{!! trans('app.cancel') !!}', '{!! trans('app.Yes') !!}'],
            }).then(function(value) {
                if (value) {
                    window.location.href = url;
                }
            });
        });
    </script>
@endpush
