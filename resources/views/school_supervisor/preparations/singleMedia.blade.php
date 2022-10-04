@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', $media->name ?? $media->source_filename)

@section('content')
    <div class="row">
        @if(!empty($media))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        @switch($mediaType)
                            @case(\App\OurEdu\GarbageMedia\MediaEnums::IMAGE_TYPE)
                                <img src="{{ getImagePath(S3Enums::LARGE_PATH . $media->filename) }}">
                                @break

                            @case(\App\OurEdu\GarbageMedia\MediaEnums::VIDEO_TYPE)
                                <video width="100%" controls>
                                    <source src="{{ getImagePath(S3Enums::LARGE_PATH . $media->filename) }}" type="video/{{ $media->extension }}">
                                    Your browser does not support the video tag.
                                </video>
                                @break

                            @case(\App\OurEdu\GarbageMedia\MediaEnums::AUDIO_TYPE)
                                <audio controls>
                                    <source src="{{ getImagePath(S3Enums::LARGE_PATH . $media->filename) }}" type="audio/{{ $media->extension }}">
                                </audio>
                                @break

                            @case(\App\OurEdu\GarbageMedia\MediaEnums::PDF_TYPE)
                                <object
                                    data="{{ getImagePath(S3Enums::LARGE_PATH . $media->filename) }}"
                                    type="application/pdf" class="w-100" style="height: 70vh;">
                                </object>
                                @break

                            @case(\App\OurEdu\GarbageMedia\MediaEnums::DOCUMENT_TYPE)
                            @if($media->mime_type == "text/plain")
                                <object
                                    data="{{ getImagePath(S3Enums::LARGE_PATH . $media->filename) }}"
                                    type="text/plain" class="w-100" style="height: 70vh;">
                                </object>
                            @else
                                <iframe
                                    src='https://docs.google.com/gview?url={{ getImagePath(S3Enums::LARGE_PATH . $media->filename) }}&embedded=true'
                                    style="width: 100%; min-height: 500px; height: 70vh;" frameborder='0'>
                                </iframe>
                            @endif
                                @break
                        @endswitch
                    </div>
                </div>
            </div>
            <div class="pull-right">
            </div>
        @if(!empty($students))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"> Students </h5>
                        <div class="table-responsive">

                       <table class="table table-striped table-bordered dt-responsive nowrap">
                           <thead>
                           <th class="text-center">Student Name</th>
                           <th class="text-center">Viewed At</th>
                           <th class="text-center">Downloaded At</th>

                           </thead>
                           @foreach($students as $student)
                               <tr>
                               <td class="text-center" >{{$student->first_name . ' ' . $student->last_name}}</td>
                               <td class="text-center" >{!! $student->pivot->viewed_at ?? "  <span aria-hidden='true'>&times;</span>" !!}</td>
                               <td class="text-center" >{!! $student->pivot->downloaded_at ?? "<span aria-hidden='true'>&times;</span>" !!}</td>
                               </tr>
                           @endforeach
                       </table>
                        </div>
                    </div>
                </div>
            </div>
                <div class="pagination justify-content-center" style="margin:auto">
                    {{$students->links()}}
                </div>
                @endif
        @else
            @include('partials.noData')
        @endif
    </div>

    @if(!empty($media->description))
    <div class="page-header">
        <h3 class="page-title">
              <span class="page-title-icon bg-gradient-primary text-white mr-2">
                <i class="mdi mdi-assistant"></i>
              </span>
            {{trans('session_preparation.Description')}}
        </h3>
        <nav aria-label="breadcrumb">
            <div class="breadcrumb">
            </div>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <p> {{ $media->description }} </p>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
