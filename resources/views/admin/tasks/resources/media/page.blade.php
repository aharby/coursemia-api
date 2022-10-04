@php
    $data = getResourceData($resource);
@endphp

    <tr>
        <th width="25%" class="text-center">{{ trans('tasks.Data') }}</th>
        <td width="75%" class="text-center">

            @if($data)
                    @if($data->page)
                        {!! $data->page !!}

                    @endif
            @else
                <h4>{{trans('tasks.No Available Details Yet')}}</h4>
            @endif

        </td>

    </tr>

