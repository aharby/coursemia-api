@extends('layouts.school_manager_layout')
@section('title', @$page_title)
@section('buttons')
    <a href="{{ route('school-account-manager.users.create')}}" class="btn btn-success">{{ trans('app.Create') }}</a>
@endsection
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
                    <th class="text-center">{{ trans('school-account-branches.Name') }}</th>
                    <th class="text-center">{{ trans('auth.ID') }}</th>
                    <th class="text-center">{{ trans('school-account-branches.Branch') }}</th>
                    <th class="text-center">{{ trans('school-account-branches.Type') }}</th>
                    <th class="text-center">{{ trans('school-account-branches.created on') }}</th>
                    <th class="text-center">{{ trans('school-account-branches.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    @if($row)
                    <tr class="text-center">
                        <td>{{ $row->name??'' }}</td>
                        <td>{{ $row->username }}</td>
                        @php

                        $userBranches = $row->branch->name ?? null;

                        if (!$userBranches) {
                            $userBranches = '';
                            foreach ($row->branches as $branch) {
                                $userBranches .= $branch->name . ', ';
                            }
                        }
                        if($row->type == \App\OurEdu\Users\UserEnums::ASSESSMENT_MANAGER){
                            $userBranches = trans('app.all_school_branches');
                        }

                        @endphp
                        <td>{{ $userBranches }}</td>
                        <td>{{ trans('app.'.$row->type) }}</td>
                        <td>{{ $row->created_at }}</td>
{{--                        <td>{{ $row->is_active? 'true':'false' }}</td>--}}
                        <td>
                            <a class="btn btn-xs btn-primary"
                               href="{{  route('school-account-manager.users.view',$row->id) }}"
                               data-toggle="tooltip" data-placement="top"
                               data-title="{{ trans('school-account-branches.view') }}">
                                {{ trans('app.View') }}
                            </a>
                            <a class="btn btn-xs btn-info"
                               href="{{  route('school-account-manager.school-account-branches.get-update-password',$row->id) }}"
                               data-toggle="tooltip" data-placement="top"
                               data-title="{{ trans('school-account-branches.edit') }}">
                                {{ trans('app.update password') }}
                            </a>

                            <a class="btn btn-xs btn-primary"
                               href="{{  route('school-account-manager.school-account-branches.get-set-role',$row->id) }}"
                               data-toggle="tooltip" data-placement="top"
                               data-title="{{ trans('school-account-branches.Set Permission') }}">
                                {{ trans('app.Set Role') }}
                            </a>
                        </td>
                    </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
                        </div></div></div></div>
            <div class="pull-right">
                {{ $rows->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
