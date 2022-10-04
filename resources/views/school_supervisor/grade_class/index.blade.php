[@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('content')
    <div class="row">
        @if(!empty($rows))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap">
                                <thead>
                                <tr>
                                    <th class="text-center">{{ trans('grade-class.grade class') }}</th>
                                    <th class="text-center">{{ trans('grade-class.Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($rows as $row)
                                    <tr class="text-center">
                                        <td>{{ $row->title  }} </td>
                                        <td>
                                            @if(can('view-educationalSystems'))
                                                <a class="btn btn-xs btn-primary"
                                                   href="{{  route('school-branch-supervisor.grade-classes.get.educational-systems',['gradeClassId' => $row->id, 'branch' => $branch->id ?? null ]) }}"
                                                   data-toggle="tooltip" data-placement="top"
                                                   data-title="{{ trans('grade-class.view') }}">
                                                    {{ trans('grade-class.Educational System') }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
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
