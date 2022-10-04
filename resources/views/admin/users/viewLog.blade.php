@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('content')
    <div class="row">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="text-center"></th>
                <th class="text-center">Before</th>
                <th class="text-center">After</th>
            <tr>
            </thead>
            <tbody>
            @foreach($row->event_properties['user'] as $key => $value)
                @if(is_array($value) || is_null($value) || in_array($key,
                    ['password', 'password_confirmation', 'old_password' ,
                    'id' , 'super_admin' , 'is_admin' , 'profile_picture' , 'confirmed',
                    'created_by' , 'created_at' ,'updated_at' , 'type']) || \Illuminate\Support\Str::contains($key, '_id'))
                    @continue
                @endif
                <tr>
                    <th class="text-center">{{ trans("users.{$key}") }}</th>
                    {{--Before--}}
                    <td class="text-center">
                    @if($value == '1')
                        {{ trans('app.Yes') }}
                    @elseif($value == '0')
                        {{ trans('app.No') }}
                    @else
                        {{ str_limit($value, 30) }}
                    @endif
                    <td class="text-center">
                        @if(isset($row->event_properties['userAttributes'][$key]))
                            @if($row->event_properties['userAttributes'][$key] == '1')
                                {{ trans('app.Yes') }}
                            @elseif($row->event_properties['userAttributes'][$key] == '0')
                                {{ trans('app.No') }}
                            @else
                                {{str_limit($row->event_properties['userAttributes'][$key] , 30)}}
                            @endif
                        @endif
                    </td>

                </tr>

            @endforeach

            @if($row->by)
                <tr>
                    <th class="text-center">{{ trans("app.Action taken by") }}</th>

                    <td  class="text-center">

                        <a href="{{ route('admin.users.get.view', $row->by->id) }}" target="_blank">
                            {{ $row->by->name }}
                        </a>

                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.users.get.view', $row->by->id) }}" target="_blank">
                            {{ $row->by->name }}
                        </a>
                    </td>
                </tr>
            @endif


            </tbody>
        </table>

    </div>
@endsection
