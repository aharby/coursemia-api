@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@section('content')
    <div class="row">
        @if(!$tasks->isEmpty())
            @foreach($tasks as $task)
                @php
                    if( $task->is_expired == 0 && $task->is_assigned == 1){
                        $color = 'btn btn-success';
                        $trans = trans('tasks.assigned but not expired');
                    }elseif ($task->is_expired == 1 && $task->is_assigned == 1){
                        $color = 'btn btn-danger';
                        $trans = trans('tasks.assigned and expired');
                    }else{
                        $color = 'btn btn-dark';
                        $trans = trans('tasks.not assigned yet');
                    }
                @endphp
                    <div class="x_panel">
                        <div class="x_content">
                            <article class="media event">
                                <a class="pull-left {{ $color }}" title="{{ $trans }}">
                                    <p class="day">{{$task->created_at->format('d')}}</p>
                                    <p class="month">{{$task->created_at->format('M Y')}}</p>
                                </a>
                                <div class="media-body">
                                    <b>{{$task->subject->name ?? null}}</b>
                                    <p>{{$task->title}}.</p>
                                </div>
                            </article>
                                <a class="btn btn-xs btn-success pull-right" href="{{  route('admin.subjects.get.task.logs',$task->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('app.Logs') }}">
                                    <i class="fa fa-bar-chart"></i>
                                </a>
                        </div>
                    </div>

            @endforeach
            <div class="pull-right">
                {{ $tasks->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
