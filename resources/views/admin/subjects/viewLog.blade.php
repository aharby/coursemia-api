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
                <th class="text-center">{{ trans("app.Before") }}</th>
                <th class="text-center">{{ trans("app.After") }}</th>
            <tr>
            </thead>
            <tbody>
            @foreach($row->event_properties['subject'] as $key => $value)
                @if(is_array($value) || is_null($value) || in_array($key,
                        ['password', 'password_confirmation', 'old_password',
                        'created_by','created_at','updated_at','total_points','practices_number','image'])|| Illuminate\Support\Str::contains($key, '_id'))
                    @continue
                @endif

                <tr>
                    <th  class="text-center">{{ trans("subjects.{$key}") }}</th>
                    {{--Before--}}

                    <td  class="text-center">
                        @if($value == '1')
                            {{ trans('app.Yes') }}
                        @elseif($value == '0')
                            {{ trans('app.No') }}
                        @else
                            {{ str_limit($value, 30) }}
                        @endif
                    </td>
                    <td class="text-center">
                        @if(isset($row->event_properties['subjectAttributes'][$key]))
                            @if($row->event_properties['subjectAttributes'][$key] == '1')
                                {{ trans('app.Yes') }}
                            @elseif($row->event_properties['subjectAttributes'][$key] == '0')
                                {{ trans('app.No') }}
                            @else
                                {{str_limit($row->event_properties['subjectAttributes'][$key] , 30)}}
                            @endif
                        @endif
                    </td>
                </tr>

            @endforeach
            @foreach($before as $key => $value)
                <tr>
                    <th class="text-center">{{ trans("subjects.{$key}") }}</th>
                    <td class="text-center">{!!  $before [$key]??''  !!}</td>
                    <td class="text-center">{!!  $after [$key] ??''  !!}</td>
                </tr>
            @endforeach

            @if($row->contentAuthors->count())
                <tr>
                    <th class="text-center">{{ trans('subjects.Content Authors') }}</th>
                    <td class="text-center">
                        @foreach($row->contentAuthors as $contentAuthor)
                            {{$contentAuthor->first_name}} {{$contentAuthor->last_name}} <br>
                        @endforeach
                    </td>
                </tr>
            @endif
            @if($row->instructors->count())

                <tr>
                    <th class="text-center">{{ trans('subjects.Instructors') }}</th>
                    <td class="text-center">
                        @foreach($row->instructors as $instructors)
                            {{$instructors->first_name}} {{$instructors->last_name}} <br>
                        @endforeach
                    </td>
                </tr>

            @endif

            @if($row->by)
                <tr>
                    <th  class="text-center">{{ trans("app.Action taken by") }}</th>

                    <td  class="text-center">

                        <a href="{{ route('admin.users.get.view', $row->by->id) }}" target="_blank">
                            {{ $row->by->name }}
                        </a>

                    </td>
                    <td  class="text-center">

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
