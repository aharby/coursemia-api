@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('buttons')
    @if($user->type == \App\OurEdu\Users\UserEnums::EDUCATIONAL_SUPERVISOR)
        <a class="btn btn-xs btn-info"
           href="{{  route('school-account-manager.school-account-branches.get-update',$user->id) }}"
           data-toggle="tooltip" data-placement="top"
           data-title="{{ trans('school-account-branches.edit') }}">
                {{ trans('educationalSupervisor.edit') }}
        </a>
    @endif
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive nowrap">
                            <tbody>

                            <tr>
                                <th width="25%" class="text-center">@lang( trans('school-account-branches.Name'))</th>
                                <td width="75%" class="text-center">{{ $user->first_name . " " . $user->last_name }}</td>
                            </tr>

                            <tr>
                                <th width="25%" class="text-center">@lang('school-account-users.username')</th>
                                <td width="75%" class="text-center">{{ $user->username }}</td>
                            </tr>

                            <tr>
                                <th width="25%" class="text-center">@lang('school-account-users.email')</th>
                                <td width="75%" class="text-center">{{ $user->email}}</td>
                            </tr>

                            <tr>
                                <th width="25%" class="text-center">@lang('school-account-users.type')</th>
                                <td width="75%" class="text-center">{{ $user->type }}</td>
                            </tr>
                            @if($user->type != \App\OurEdu\Users\UserEnums::ASSESSMENT_MANAGER)
                                <tr>
                                    <th width="25%" class="text-center">@lang('school-account-users.branch')</th>
                                    <td width="75%" class="text-center">{{ $userBranch ?? '' }}</td>
                                </tr>
                            @endif

                            <tr>
                                <th width="25%" class="text-center">@lang('school-account-users.Created at')</th>
                                <td width="75%" class="text-center">{{ $user->created_at ?? '' }}</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
