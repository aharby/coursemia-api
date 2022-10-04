@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('content')
    <div class="row">
        @include("school_supervisor.preparations._filter")
        @if(!empty($mediaLibrary))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            @foreach($mediaLibrary as $singleMedia)
                                <div class="col-4">
                                        <div class="subject-progress card-height">
                                            <div class="up">
                                                <div class="img m-auto">
                                                    <i class="fas fa-{{\App\OurEdu\GarbageMedia\MediaEnums::getTypeExtensionsIconDisplay($singleMedia->extension)["icon"]}}"></i>
                                                </div>
                                            </div>
                                            <h3 class="resource-title h3 text-center">{{ substr($singleMedia->name ?? $singleMedia->source_filename,0 , 20) . (strlen($singleMedia->name ?? $singleMedia->source_filename) > 20 ? "..." : "") }}</h3>
                                            <div class="row">
                                                <div class="col-6">
                                                    <ul>
                                                        <li>{{ trans("session_preparation.classroom") }}: {{ $singleMedia->sessionPreparation->classroom->name ?? "" }}</li>
                                                        <li>{{ trans("session_preparation.subject") }}: {{ $singleMedia->sessionPreparation->subject->name ?? ""}}</li>
                                                        <li>{{ trans("session_preparation.status") }}: @if($singleMedia->sessionPreparation->published_at) <span class="badge badge-success">{{ trans("session_preparation.Published") }}</span> @else <span class="badge badge-danger">{{ trans("session_preparation.Not Published") }}</span>@endif </li>
                                                    </ul>
                                                </div>
                                                <div class="col-6">
                                                    <ul>
                                                        <li>{{ trans("session_preparation.date") }}: {{ $singleMedia->sessionPreparation->session->from_date ?? "" }}</li>
                                                        <li>{{ trans("session_preparation.time") }}: [{{ $singleMedia->sessionPreparation->session->from_time ?? "" }} : {{ $singleMedia->sessionPreparation->session->to_time ?? "" }}]</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="down">
                                                <a class="button text-center btn-block mt-3 m-0" href="{{ route('school-branch-supervisor.session.preparation.get.single.media', $singleMedia) }}" class="btn btn-primary">{{ trans("session_preparation.view") }}</a>
                                            </div>
                                        </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
            <div class="pull-right">
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
@push('scripts')
    <script>
        $("#classroom_id").change(function () {
            $('#classroomClass_id').empty();
            $('#classSession_id').empty();
            $('#classroomClass_id').append(`<option value="" selected> {{ trans("session_preparation.timetable") }} </option>`);
            $('#classSession_id').append(`<option value="" selected> {{ trans("session_preparation.sessions") }} </option>`);
            let classroom_id = $(this).val();
            $.get('{{ route('school-branch-supervisor.classrooms.classroomClasses.byClassroom') }}' + `/${classroom_id}`,
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'classroom_id': $(this).val(),
                })
                .done(function (response) {

                    $.each(response.classes, function (i, item) {
                        $('#classroomClass_id').append(`<option value="${item.id}">${item.instructor.first_name} ${item.instructor.last_name} - ${item.subject.name} - (${item.from_time} - ${item.to_time}) </option>`);
                    });
                });
        });
        @if(request()->old('classroom_id'))
        $('#classroom_id').trigger('change');
        @endif


        $("#classroomClass_id").change(function () {
            $('#classSession_id').empty();
            $('#classSession_id').append(`<option value="" selected> {{ trans("session_preparation.sessions") }} </option>`);
            let classroomClass_id = $(this).val();
            $.get('{{ route('school-branch-supervisor.classrooms.classroomClasses.sessions') }}' + `/${classroomClass_id}`,
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'classroomClass_id': $(this).val(),
                })
                .done(function (response) {

                    $.each(response.sessions, function (i, item) {
                        let from = new Date(item.from);
                        $('#classSession_id').append(`<option value="${item.id}">${from.toLocaleDateString()} </option>`);
                    });
                });
        });
        @if(request()->old('classroomClass_id'))
        $('#classroomClass_id').trigger('change');
        @endif

    </script>
@endpush
