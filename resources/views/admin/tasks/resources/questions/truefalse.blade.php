@php
    $data = getResourceData($resource);
@endphp
@if($data)
    <tr>
        <th width="25%" class="text-center">{{ trans('tasks.Number of Questions') }}</th>
        <td width
        ="75%" class="text-center">{{ $data->questions()->count() }}</td>
    </tr>
@else
    <h4>{{trans('tasks.No Available Details Yet')}}</h4>
@endif
