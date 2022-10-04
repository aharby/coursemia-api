@php
    $data = getResourceData($resource);
@endphp

    <tr>
        <th width="25%" class="text-center">{{ trans('tasks.Data') }}</th>
        <td width="75%" class="text-center">

            @if($data)
                    @if($data->link)
                        <i class="fa fa-paperclip"></i> <a href="{{$data->link}}" target="_blank">{{trans('task.Check It Here')}}</a>

                    @else

                        @foreach($data->media as $media)
                            <i class="fa fa-paperclip"></i> <a href="{{ Storage::URL("uploads/large/$media->filename") }}" target="_blank">{{trans('task.Check It Here')}}</a>
                        @endforeach

                    @endif
            @else
                <h4>{{trans('tasks.No Available Details Yet')}}</h4>
            @endif

        </td>

    </tr>

