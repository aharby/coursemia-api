@php
    $data = getResourceData($resource);
@endphp

    <tr>
        <th width="25%" class="text-center">{{ trans('tasks.Data') }}</th>
        <td width="75%" class="text-center">

            @if($data)
                    
                @foreach($data->media as $media)
                	<a href="{{ Storage::URL("uploads/large/$media->filename") }}" target="_blank">
						<img src="{{ Storage::URL("uploads/large/$media->filename") }}" width="120" alt="">
                	</a>
                @endforeach

            @else
                <h4>{{trans('tasks.No Available Details Yet')}}</h4>
            @endif

        </td>

    </tr>

