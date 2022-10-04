@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('content')
    <style>
            .card{
                background: #e7f1e4 !important;
            }


    </style>
    <div class="row">
        @if(!empty($session->preparation))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card" style="background: transparent !important">
                    <div class="card mt-5 mb-5 p-5">
                        <div class="card-title">
                            <h3> {{ trans("session_preparation.internal_preparation") }}</h3>
                        </div>
                        <div class="card-body bg-white">
                            {!! $session->preparation->internal_preparation !!}
                        </div>
                    </div>
                    <div class="card" >
                        <div class="card-body">
                            <div class="card-title">
                                <h3> {{ trans("session_preparation.student_preparation") }}</h3>
                            </div>
                            <div class="card mb-5">
                                <div class="card-title">
                                    <h4> {{ trans("session_preparation.objectives") }}</h4>
                                </div>
                                <div class="card-body bg-white">
                                    {!!  $session->preparation->section_id ? $session->preparation->section->title : $session->preparation->objectives !!}
                                </div>
                            </div>
                            <hr>
                            <div class="card mb-5 clearfix" >
                                <div class="card-title">
                                    <h4> {{ trans("session_preparation.pre_Learning") }}</h4>
                                </div>
                                <div class="card-body bg-white">
                                    {!! $session->preparation->pre_Learning !!}
                                </div>
                            </div>
                            <hr>
                            <div class="card mb-5 clearfix" >
                                <div class="card-title">
                                    <h4> {{ trans("session_preparation.Introductory") }}</h4>
                                </div>
                                <div class="card-body bg-white">
                                    {!! $session->preparation->introductory !!}
                                </div>
                            </div>
                            <hr>
                            <div class="card mb-5 clearfix" >
                                <div class="card-title">
                                    <h4> {{ trans("session_preparation.Application") }}</h4>
                                </div>
                                <div class="card-body bg-white">
                                    {!! $session->preparation->application !!}
                                </div>
                            </div>
                            <hr>
                            <div class="card mb-5 clearfix" >
                                <div class="card-title">
                                    <h4> {{ trans("session_preparation.Evaluation") }}</h4>
                                </div>
                                <div class="card-body bg-white">
                                    {!! $session->preparation->evaluation !!}
                                </div>
                            </div>
                            @if($session->preparation->media)
                            <hr>
                            @endif
                        </div>
                        @if($session->preparation->media)
                            <div class="card mb-5 clearfix" >
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>{{ trans("session_preparation.media_files") }}</h4>
                                </div>
                                <div class="row">
                                    @foreach($session->preparation->media as $singleMedia)
                                        <div class="col-md-6 col-lg-4 col-sm-6 col-xs-6 ">
                                            <div class="subject-progress card-height">
                                                <div class="up">
                                                    <div class="img m-auto">
                                                        <i class="fas fa-{{\App\OurEdu\GarbageMedia\MediaEnums::getTypeExtensionsIconDisplay($singleMedia->extension)["icon"]}}"></i>
                                                    </div>
                                                </div>
                                                <h3 class="resource-title h3 text-center">{{ substr($singleMedia->name ?? $singleMedia->source_filename,0 , 20) . (strlen($singleMedia->name ?? $singleMedia->source_filename) > 20 ? "..." : "") }}</h3>
                                                <div class="row">
                                                    <div class="col-xl-6 col-lg-12 col-sm-12 ">
                                                        <ul>
                                                            <li>{{ trans("session_preparation.classroom") }}
                                                                : {{ $singleMedia->sessionPreparation->classroom->name ?? "" }}</li>
                                                            <li>{{ trans("session_preparation.subject") }}
                                                                : {{ $singleMedia->sessionPreparation->subject->name ?? ""}}</li>
                                                            <li>{{ trans("session_preparation.status") }}
                                                                : @if($singleMedia->sessionPreparation->published_at)
                                                                    <span
                                                                        class="badge badge-success">{{ trans("session_preparation.Published") }}</span> @else
                                                                    <span
                                                                        class="badge badge-danger">{{ trans("session_preparation.Not Published") }}</span>@endif
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-xl-6 col-lg-12 col-sm-12">
                                                        <ul>
                                                            <li>{{ trans("session_preparation.date") }}
                                                                : {{ $singleMedia->sessionPreparation->session->from_date ?? dd($singleMedia->sessionPreparation) }}</li>
                                                            <li>{{ trans("session_preparation.time") }}:
                                                                [{{ $singleMedia->sessionPreparation->session->from_time ?? "" }}
                                                                : {{ $singleMedia->sessionPreparation->session->to_time ?? "" }}
                                                                ]
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <a class="button text-center btn-block mt-3 m-0"
                                                           href="{{ route('school-branch-supervisor.session.preparation.get.single.media', $singleMedia) }}"
                                                           class="btn btn-primary">{{ trans("session_preparation.view") }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="pull-right">
                </div>
                @else
                    @include('partials.noData')
                @endif
            </div>
@endsection
