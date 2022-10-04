@include('form.input',[
    'type'=>'text',
    'name'=>'first_name',
    'value'=> $user->first_name ?? null,
    'attributes'=> [
        'class'=>'form-control',
         'label'=>trans('school-account-users.first name'),
         'placeholder'=>trans('school-account-users.first name'),
         'required'=>'required'
     ]
 ])


@include('form.input',[
    'type'=>'text',
    'name'=>'last_name',
    'value'=> $user->last_name ?? null,
    'attributes'=> [
        'class'=>'form-control',
         'label'=>trans('school-account-users.last name'),
         'placeholder'=>trans('school-account-users.last name'),
         'required'=>'required'
     ]
 ])


@include('form.input',[
    'type'=>'text',
    'name'=>'username',
    'value'=> $user->username ?? null,
    'attributes'=> [
        'class'=>'form-control',
        @$user ? 'disabled' : '',
        'label'=>trans('school-account-users.username'),
        'placeholder'=>trans('school-account-users.username'),
        'required'=>'required'
    ]
])

@include('form.input',[
    'type'=>'email',
    'name'=>'email',
    'value'=> $user->email ?? null,
    'attributes'=> [
        'class'=>'form-control',
        @$user ? 'disabled' : '',
        'label'=>trans('school-account-users.email'),
        'placeholder'=>trans('school-account-users.email'),
    ]
])


@include('form.select',[
    'name'=>'type',
    'options'=> $userEnum::schoolAccountUsers() ,
    'value'=> $user->type ?? 'null',
    'attributes' => [
        'id'=>'school_account_user_type',
        'class'=>'form-control',
        'label'=>trans('school-account-users.type'),
        'placeholder'=>trans('school-account-users.type')
    ]
])

<div id="parent_school_account_roles" @if (old("type", isset($user) ? $user->type : null) != $userEnum::ACADEMIC_COORDINATOR) style="display: none @endif">
    @include('form.select',[
        'name'=>'role_id',
        'options'=> $schoolAccountRoles ,
        'value'=> $user->role_id?? 'null',
        'attributes' => [
            'id'=>'school_account_roles',
            'class'=>'form-control',
            'label'=>trans('school-account-users.roles'),
            'placeholder'=>trans('school-account-users.roles')
        ]
    ])
</div>

<div id="select_single_branch" @if (old("type", isset($user) ? $user->type : null) == $userEnum::EDUCATIONAL_SUPERVISOR) style="display: none" @endif>
@include('form.select',[
    'name'=>'branch_id',
    'options'=> $schoolAccountBranches,
    'value'=> $user->branch_id ?? 'null',
    'attributes' => [
        'id'=>'branch_id',
        'class'=>'form-control',
        'label'=>trans('school-account-users.branch'),
        'placeholder'=>trans('school-account-users.branch')
    ]
])
</div>

<div id="select_multi_branch" @if (old("type", isset($user) ? $user->type : null) != $userEnum::EDUCATIONAL_SUPERVISOR) style="display: none" @endif>
@include('form.multiselect',[
    'name'=>'branch_id[]',
    'options'=> $schoolAccountBranches,
    'value'=> $user->branch_id ?? 'null',
    'attributes' => [
        'id'=>'branches_id',
        'class'=>'form-control select2',
        'label'=>trans('school-account-users.branch'),
    ]
])
</div>

@push('scripts')
    <script>

        function hideDropDowns() {
           $("#parent_school_account_roles").hide();
           $("#select_single_branch").hide();
           $("#select_multi_branch").hide();
        }

        $(function() {

            @if (old("type", isset($user) ? $user->type : null) != $userEnum::ACADEMIC_COORDINATOR)
                $('#parent_school_account_roles').hide();
            @endif

            let userType = $('#school_account_user_type').val();
            if(userType == "{{ $userEnum::ASSESSMENT_MANAGER }}"){
                hideDropDowns();
            }

            $('#school_account_user_type').change(function(e) {
                var type = $(this).val();
                hideDropDowns()

                if (type == "{{ $userEnum::ACADEMIC_COORDINATOR }}") {
                    $("#parent_school_account_roles").show();
                }
                if (type == "{{ $userEnum::ASSESSMENT_MANAGER }}") {
                     hideDropDowns()
                }


                if (type == "{{ $userEnum::EDUCATIONAL_SUPERVISOR }}") {
                    $("#select_multi_branch").show();
                } else if(type != "{{ $userEnum::ASSESSMENT_MANAGER }}"){
                    $("#select_single_branch").show();
                }

            });
        });
    </script>
@endpush
