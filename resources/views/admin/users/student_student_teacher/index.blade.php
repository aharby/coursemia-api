@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.users.get.add.student-teacher', $student->id) }}" class="btn btn-success">{{ trans('users.Add Student Teacher') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">

        @if($student->teachers()->exists())
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('users.Name') }}</th>
                    <th class="text-center">{{ trans('users.Assigned Subjects') }}</th>
                    <th class="text-center">{{ trans('users.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($student->teachers as $teacher)
                    <tr class="text-center">
                        <td>{{ $teacher->name }} </td>
                        <td>
                            @foreach($teacher->studentTeacherSubjects()->where('student_id', $student->id)->first()->subjects as $subject)
                                {{ $subject->name }} <br>
                            @endforeach
                        </td>
                        <td>
                            <div class="row">
                                <div class="form-group">
                                    <form method="POST" action="{{route('admin.users.detach.student-teacher' ,
                                                                ['studentId' => $student->id, 'studentTeacherId' => $teacher->id])}}">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button type="submit" class="btn btn-xs btn-danger" value="Remove Relation"
                                                data-confirm="{{trans('app.Are you sure you want to detach this item')}}?">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
