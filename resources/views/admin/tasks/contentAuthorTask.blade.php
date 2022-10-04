@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('content')
    <div class="row">
        @if(!$authors->isEmpty())
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                <tr>
                    <th>{{trans('tasks.Author Name')}} </th>
                    <th>{{trans('tasks.Task Count')}}</th>
                    <th>{{trans('tasks.Done Tasks')}} </th>
                    <th>{{trans('tasks.Not Expired Tasks')}}</th>
                    <th>{{trans('tasks.Expired Tasks')}}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($authors as $author)
                    <tr>
                        <td>{{ $author->name }}</td>
                        <td>{{$author->tasksCount}}</td>
                        <td>{{$author->doneTasks}}</td>
                        <td>{{$author->notExpiredTasks}}</td>
                        <td>{{$author->expiredTasks}}</td>

                        <td>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2 form-group">
                                    <a class="btn btn-xs btn-primary" href="{{  route('admin.tasks.get.content.author.tasks.details', $author->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('subject.view') }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pull-right">
                {{ $authors->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
