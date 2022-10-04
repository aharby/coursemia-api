@isset($errors)
    @if($errors->has($name))
        <span class="text-danger">{{ $errors->get($name)[0] }}</span>
    @endif
@endisset
