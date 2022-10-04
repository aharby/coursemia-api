@extends('layouts.school_manager_layout')
@section('title', @$page_title)
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        {!! Form::open(['url' => Route('school-account-manager.school-account-branches.post-set-role' , [$row->id])]) !!}
                        <div class="form-group">
                            <label class="form-control-label">{{ trans('roles.Roles') }}<span class="text-danger">*</span></label>
                            <div class="select2-remove">
                                <select name="role_id" id="role" class="form-control" style="width: 100%">
                                    @if(is_null($row))
                                        <option value="">{{ trans('roles.Roles') }}</option>
                                    @endif
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}"
                                                {{ $row->role_id == $role->id ? 'selected' : '' }} class="role role-{{$role->type}}">{{ $role->title }}</option>
                                    @endforeach
                                </select>
                                @if(@$errors)
                                    @foreach($errors->get('role_id') as $message)
                                        <span class='help-inline text-danger'>{{ $message }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-gradient-success btn-sm">
                                <i class="mdi mdi-content-save"></i>{{ trans('app.Save') }}
                            </button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
