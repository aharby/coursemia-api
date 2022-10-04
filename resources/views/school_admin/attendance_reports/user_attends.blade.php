@extends('layouts.school_admin_layout')
@section('title', @$page_title)
@section('buttons')
    @if(count($rows))
        <a href="{{ route("school-admin.attendance-reports.user-attends.export", array_merge(["branch" => $branch ?? null], request()->all())) }}"
           target="_blank" class="btn btn-md btn-success align-right">{{ trans("app.Export") }}</a>
    @endif
@endsection

@section('content')
    <div class="row">
        @include('school_admin.attendance_reports._filter')
        @if(!empty($rows))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap">
                                <thead>
                                <tr>
                                    <th class="text-center">{{ trans('school-account-branches.User Name') }}</th>
                                    <th class="text-center">{{ trans('school-account-branches.User ID') }}</th>
                                    <th class="text-center">{{ trans('school-account-branches.Branch') }}</th>
                                    <th class="text-center">{{ trans('school-account-branches.User Type') }}</th>
                                    <th class="text-center">{{ trans('school-account-branches.Sessions') }}</th>
                                    <th class="text-center">{{ trans('school-account-branches.attends') }}</th>
                                    <th class="text-center">{{ trans("app.Export") . " " . trans('school-account-branches.Sessions')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($rows as $row)
                                    @if($row)
                                        <tr class="text-center">
                                            <td>{{ $row->name??'' }}</td>
                                            <td>{{ $row->username }}</td>
                                            <td>@if(!is_null($branch))
                                                    {{$branch}}
                                                @else
                                                    @if($row->branches->count()>0)
                                                        {{implode(',',$row->branches->pluck('name')->toArray())}}
                                                    @else
                                                        {{ $row->branch->name ?? $row->schoolAccountBranchType->name ?? '' }}
                                                    @endif
                                                @endif


                                            </td>
                                            <td>{{trans('app.'.$row->type) }}</td>
                                            <td>
                                                <div style="max-height: 120px;overflow-y: scroll;overflow-x: hidden;">
                                                    @foreach($row->VCRSessionsPresence as $sessionPresence)
                                                        <div style="margin-bottom: 10px;border-radius: 10px;background-color: #585757;box-shadow: 1px 1px 1px #d0c2c2;padding:8px">

                                                            <div class="text-center"
                                                                 style="margin-bottom: 4px;background-color: #1b7943;color:#fff;border-radius: 10px;">
                                                                <h4> {{ trans('reports.classroom') }}
                                                                    : {{ optional($sessionPresence->vcrSession->classroom)->name }} </h4>
                                                            </div>
                                                            <div class="text-center"
                                                                 style="margin-bottom: 4x;background-color: #0a0302;color:#fff;border-radius: 10px;text-align: center;">
                                                                <h4> {{ trans('reports.branch') }}
                                                                    : {{ optional($sessionPresence->vcrSession->classroom->branch)->name }} </h4>
                                                            </div>
                                                            <div class="text-center"
                                                                 style="margin-bottom: 4x;background-color: #0a0302;color:#fff;border-radius: 10px;text-align: center;">
                                                                <h4> {{ trans('reports.subject') }}
                                                                    : {{ optional($sessionPresence->vcrSession->subject)->name }} </h4>
                                                            </div>
                                                            <div class="text-center"
                                                                 style="margin-bottom: 4px;background-color: #0a1520;color:#fff;border-radius: 10px;text-align: center;">
                                                                <h4> {{ trans('reports.instructor') }}
                                                                    : {{ optional($sessionPresence->vcrSession->instructor)->first_name }} </h4>
                                                            </div>
                                                            <div class="text-center"
                                                                 style="margin-bottom: 4px;background-color: #0b2e13;color:#fff;border-radius: 10px;text-align: center;">
                                                                <h4> {{ trans('reports.from') }}
                                                                    : {{ optional($sessionPresence->vcrSession->classroomClassSession)->from_time }} </h4>
                                                            </div>
                                                            <div class="text-center"
                                                                 style="margin-bottom: 4px;background-color: #0b2e13;color:#fff;border-radius: 10px;text-align: center;">
                                                                <h4> {{ trans('reports.to') }}
                                                                    : {{ optional($sessionPresence->vcrSession->classroomClassSession)->to_time   }} </h4>
                                                            </div>
                                                            <div class="text-center"
                                                                 style="margin-bottom: 4px;background-color: #0b2e13;color:#fff;border-radius: 10px;text-align: center;">
                                                                <h4> {{ trans('reports.date') }}
                                                                    : {{  optional($sessionPresence->vcrSession->classroomClassSession)->from_date  }} </h4>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>{{ $row->v_c_r_sessions_presence_count }}</td>
                                            <td>
                                                @if($row->v_c_r_sessions_presence_count > 0)
                                                    <a href="{{ route("school-admin.attendance-reports.user-attends.sessions.export", $row) }}"
                                                       target="_blank"
                                                       class="btn btn-md btn-success align-right">{{ trans("app.Export") }}</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                {{ $rows->withQueryString()->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection

@push('scripts')
    <script>

        $('#classroom').change(function (e) {
            var classroom_id = $(this).val();
            $('#subjects').empty();
            $('#subjects').append(`<option value="" selected> {{ trans('reports.subject') }} </option>`);
            $.get('{{ route('school-account-manager.manager-reports.get-classroom-subjects') }}',
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'classroom_id': classroom_id,
                })
                .done(function (response) {
                    $.each(response.subjects, function (i, item) {
                        $('#subjects').append('<option value="' + i + '">' + item + '</option>');
                    });
                });
        });
        $(document).ready(function () {
            $('#from_date').datepicker({
                maxDate: 0
            });
            $('#to_date').datepicker({
                maxDate: 0
            });
        });
    </script>
@endpush

