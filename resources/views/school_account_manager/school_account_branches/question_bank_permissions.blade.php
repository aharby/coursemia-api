@extends('layouts.school_manager_layout')
@section('title', @$page_title)
@section('content')
    <div class="row">
        @include("school_account_manager.school_account_branches.subjects_filter")
        @if(!empty($subjects))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap">
                                <thead>
                                <tr>
                                    <th class="text-center">{{ trans('school-account-branches.Subject') }}</th>
                                    <th class="text-center">{{ trans('school-account-branches.Branch') }}</th>
                                    <th class="text-center">{{ trans('app.Educational Systems') }}</th>
                                    <th class="text-center">{{ trans('app.Grade Classes') }}</th>
                                    <th class="text-center">{{ trans('school-account-branches.Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($subjects as $subject)
                                    <tr class="text-center">
                                        <td>{{ $subject->name }}</td>
                                        <td>{{ $branch->name }}</td>
                                        <td>{{ $subject->educationalSystem?->name }}</td>
                                        <td>{{ $subject->gradeClass?->title }}</td>
                                        <td>
                                            <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#Permissions_{{ $subject->id }}">{{ trans('school-account-branches.Permissions') }}</button>
                                        </td>
                                    </tr>

                                    <div class="modal fade" tabindex="-1" data-backdrop="static" role="dialog" id="Permissions_{{ $subject->id }}">
                                        <div class="modal-dialog" style="max-width: 80%; height: 70%" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <div class="row">
                                                        <h5 class="modal-title px-3">{{ $subject->name }}</h5>
                                                    </div>
                                                </div>

                                                <div class="modal-body">
                                                    <form action="{{ route("school-account-manager.school-account-branches.branch.questions.branch.subjects.permissions", $subject->id) }}" method="post">
                                                        @csrf
                                                        <input type="hidden" value="{{ $branch->id }}" name="branch_id">
                                                        <div class="form-group row">
                                                            <div class="col-sm-2">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <div class="form-check">
                                                                    <input name="permission_scope"
                                                                           {{ (count($subject->branchQuestionsPermissions) and $subject?->branchQuestionsPermissions[0]?->pivot?->school_scope) ? 'checked' : ''}}
                                                                           value="school_scope" class="form-check-input" type="radio" id="school_scope_{{ $subject->id }}">
                                                                    <label class="form-check-label" for="school_scope_{{ $subject->id }}">

                                                                        {{ trans('school-account-branches.School scope') }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <div class="form-check">
                                                                    <input name="permission_scope"
                                                                    {{ (count($subject->branchQuestionsPermissions) and $subject?->branchQuestionsPermissions[0]?->pivot?->branch_scope) ? 'checked' : ''}}
                                                                           value="branch_scope" class="form-check-input" type="radio" id="branch_scope_{{ $subject->id }}">
                                                                    <label class="form-check-label" for="branch_scope_{{ $subject->id }}">

                                                                        {{ trans('school-account-branches.Branch scope') }}
                                                                    </label>
                                                                </div>
                                                            </div>
{{--                                                            <div class="col-sm-4">--}}
{{--                                                                <div class="form-check">--}}
{{--                                                                    <input name="permission_scope" value="grade_scope" class="form-check-input"--}}
{{--                                                                    {{ (count($subject->branchQuestionsPermissions) and $subject?->branchQuestionsPermissions[0]?->pivot?->grade_scope) ? 'checked' : ''}}--}}
{{--                                                                           type="radio" id="grade_scope_{{ $subject->id }}">--}}
{{--                                                                    <label class="form-check-label" for="grade_scope_{{ $subject->id }}">--}}

{{--                                                                        {{ trans('school-account-branches.Grade scope') }}--}}
{{--                                                                    </label>--}}
{{--                                                                </div>--}}
{{--                                                            </div>--}}

                                                            <div class="col-sm-1">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-sm-10">
                                                                <button type="submit" class="btn btn-primary" id="form-submit">{{ trans("app.Save") }}</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                </tbody>
                            </table>
                        </div></div></div></div>
            <div class="pull-right">
                {{ $subjects->appends(request()->all())->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif


    </div>
@endsection

@push("scripts")

    <script>
        $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "inherit" );
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "auto" );
        })

    </script>


@endpush
