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
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <article class="media event">
                                <a class="pull-left {{ $color }}" title="{{ $trans }}">
                                    <p class="day">{{$task->created_at->format('d')}}</p>
                                    <p class="month">{{$task->created_at->format('M Y')}}</p>
                                </a>
                                <div class="media-body">
                                    <b>{{$task->subject->name ?? ''}}</b>
                                    <p>{{$task->title}}.</p>
                                        <p>
                                            <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample{{$loop->index}}" role="button" aria-expanded="false" aria-controls="collapseExample">
                                                {{trans('tasks.Details')}}
                                            </a>
                                        </p>
                                        <div class="collapse" id="collapseExample{{$loop->index}}">
                                            <div class="container container-fluid" style="background: lightgray; border-radius: 5px;padding: 10px">
                                                @include('admin.tasks.resources.index' , ['resource' => $task->resourceSubjectFormatSubject, 'task' => $task, 'contentAuthor' => $contentAuthor ])
                                            </div>
                                        </div>
                                </div>
                            </article>
                        </div>
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
