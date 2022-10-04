@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@section('content')
    <div class="row">


        @if(!$rows->isEmpty())
            @foreach($rows as $row)
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <article class="media event">
                                <a class="pull-left btn btn-dark">
                                    <p class="day">{{$row->created_at->format('d')}}</p>
                                    <p class="month">{{ trans("months.{$row->created_at->format('M')}") }} {{$row->created_at->format('Y')}}</p>
                                </a>
                                <div class="media-body">
                                    <b>{{ trans("events.{$row->event}") }}

                                    </b>
                                    <p>
                                        <a class="btn btn-primary" data-toggle="collapse"
                                           href="#collapseExample{{$loop->index}}" role="button" aria-expanded="false"
                                           aria-controls="collapseExample">
                                            {{trans('app.Details')}}
                                        </a>
                                    </p>
                                    <div class="collapse" id="collapseExample{{$loop->index}}">
                                        <div class="container container-fluid"
                                             style="background: lightgray; border-radius: 5px;padding: 10px">
                                            <div class="panel">
                                                <div class="panel-heading">
                                                    <div class="panel-title"><h3>{{ trans('app.Log details') }}</h3>
                                                    </div>
                                                </div>
                                                <div class="panel-body">
                                                    @if($row->user_id)
                                                        <strong>  {{ trans("app.Action taken by") }}: </strong>
                                                        <a href="{{ route('admin.users.get.view', $row->user->id) }}"
                                                           target="_blank">
                                                            {{ $row->user->name }}
                                                        </a>

                                                    @endif

                                                    <table class="table table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th class="text-center"></th>
                                                            <th class="text-center">{{ trans("app.Before") }}</th>
                                                            <th class="text-center">{{ trans("app.After") }}</th>
                                                        <tr>
                                                        </thead>
                                                        <tbody>


                                                        @foreach($row->new_values as $key => $value)

                                                            @if(is_array($value) || is_null($value) || in_array($key, ['password', 'password_confirmation', 'old_password']) || \Illuminate\Support\Str::contains($key, '_id'))
                                                                @continue
                                                            @endif
                                                            <tr>
                                                                <th  class="text-center">{{ trans("users.{$key}") }}</th>
                                                                {{--Before--}}

                                                                <td  class="text-center">
                                                                    @if(isset($row->old_values[$key]))
                                                                        @if($row->old_values[$key] == '1')
                                                                            {{ trans('app.Yes') }}
                                                                        @elseif($row->old_values[$key] == '0')
                                                                            {{ trans('app.No') }}
                                                                        @else
                                                                            {{str_limit($row->old_values[$key] , 30)}}
                                                                        @endif
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    @if(isset($row->new_values[$key]))
                                                                        @if($row->new_values[$key] == '1')
                                                                            {{ trans('app.Yes') }}
                                                                        @elseif($row->new_values[$key] == '0')
                                                                            {{ trans('app.No') }}
                                                                        @else
                                                                            {{str_limit($row->new_values[$key] , 30)}}
                                                                        @endif
                                                                    @endif
                                                                </td>
                                                            </tr>

                                                        @endforeach


                                                        </tbody>
                                                    </table>

                                                    <div class="row">


{{--                                                        <div class="col-sm-12 col-md-6 text-center">--}}
{{--                                                            <a href="{{ route('admin.tasks.get.view', request()->route('id')) }}"--}}
{{--                                                               class="btn btn-success" target="_blank">--}}
{{--                                                                {{ trans('tasks.View task') }}--}}
{{--                                                            </a>--}}
{{--                                                        </div>--}}

                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="pull-right">
                {{ $rows->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
