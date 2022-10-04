<style>
    .sidebar .nav .nav-item:hover {
        background: #f2edf3;
    }
    .sidebar .nav.sub-menu .nav-item .nav-link {
        padding: .75rem .50rem;
    }
    .sidebar .nav .nav-item {
        padding: 0 1rem;
    }
</style>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        @if(auth()->user()->type == \App\OurEdu\Users\UserEnums::SCHOOL_ACCOUNT_MANAGER)
                <li class="nav-item {{(request()->is('*/school-account-branches*'))?"active":""}}">
                    <a class="nav-link" href="{{ route('school-account-manager.school-account-branches.get.index') }}">
                        <span class="menu-title">{{ trans('navigation.School Branches') }}</span>
                        <i class="mdi mdi-assistant menu-icon"></i>
                    </a>
                </li>

                <li class="nav-item {{(request()->is('*/branch-grade-classes*'))?"active":""}}">
                <a class="nav-link" href="{{ route('school-account-manager.branch-grade-classes.get.index') }}">
                    <span class="menu-title">{{ trans('navigation.Assign Branch Data') }} </span>
                    <i class="mdi mdi-assistant menu-icon"></i>
                </a>
              </li>

                <li class="nav-item {{(request()->is('*/users*'))?"active":""}}">
                <a class="nav-link" href="{{ route('school-account-manager.school-account-branches.get.users') }}">
                    <span class="menu-title">{{ trans('navigation.Users') }} </span>
                    <i class="mdi mdi-assistant menu-icon"></i>
                </a>
                </li>



                <li class="nav-item {{(request()->is('*/preperation'))?"active":""}}">
                    <a class="nav-link" href="{{ route('school-account-manager.session.preparation.get.media.library') }}">
                        <span class="menu-title">{{ trans('navigation.Media Library') }}</span>
                        <i class="mdi mdi-assistant menu-icon"></i>
                    </a>
                </li>

                <li class="nav-item {{(request()->is('*/roles*'))?"active":""}}">
                <a class="nav-link" href="{{ route('school-account-manager.roles.index') }}">
                    <span class="menu-title">{{ trans('navigation.Roles') }} </span>
                    <i class="mdi mdi-assistant menu-icon"></i>
                </a>
               </li>

{{--                <li class="nav-item {{(request()->is('*/quizzes*'))?"active":""}}">--}}
{{--                <a class="nav-link" href="{{ route('school-account-manager.school.manager.quizzes.index') }}">--}}
{{--                    <span class="menu-title">{{ trans('navigation.quizzes') }} </span>--}}
{{--                    <i class="mdi mdi-assistant menu-icon"></i>--}}
{{--                </a>--}}
{{--               </li>--}}

                <li class="nav-item {{(request()->is('*/general-quizzes/*'))?"active":""}}">
                    <a class="nav-link" href="{{ route('school-account-manager.general-quizzes.index') }}">
                        <span class="menu-title">{{ trans('navigation.Quizzes Reports') }}</span>
                        <i class="mdi mdi-assistant menu-icon"></i>
                    </a>
                </li>

                <li class="nav-item {{(request()->is('*/trashed/*'))?"active":""}}">
                    <a class="nav-link" href="{{ route('school-account-manager.general-quizzes.classrooms.trashed') }}">
                        <span class="menu-title">{{ trans('navigation.trashed_classrooms_reports') }}</span>
                        <i class="mdi mdi-assistant menu-icon"></i>
                    </a>
                </li>

                <li class="nav-item {{(request()->is('*/total-percentages-report'))?"active":""}} ">
                    <a class="nav-link" href="{{ route('school-account-manager.general-quizzes-reports.total.percentages.report') }}">
                        <span class="menu-title">{{ trans("navigation.total percentages report") }} </span>
                        <i class="mdi mdi-assistant menu-icon"></i>
                    </a>
                </li>

                <li class="nav-item {{request()->is('*/branch-reports-*') ? 'active' : null}}">
                    <a class="nav-link" data-toggle="collapse" href="#campaigner-pages" aria-expanded="true" aria-controls="general-pages">
                        <span class="menu-title">{{ trans("navigation.branches_reports") }}</span>
                        <i class="menu-arrow"></i>
                        <i class="mdi mdi-assistant menu-icon"></i>
                    </a>
                    <div class="collapse show" id="campaigner-pages" style="">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('school-account-manager.general-quizzes-reports.branch.reports.instructor.levels') }}">{{ trans("navigation.instructor level report") }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('school-account-manager.general-quizzes-reports.branch.reports.subject.levels') }}">{{ trans("navigation.subject level report") }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('school-account-manager.general-quizzes-reports.branch.reports.skill.levels') }}">{{ trans("navigation.Skill Level Report") }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('school-account-manager.general-quizzes-reports.question.percentage.report.index') }}">{{ trans("navigation.questions Percentages") }}</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item {{Route::is('*branch-level-report') ? 'active' : null}}">
                    <a class="nav-link" data-toggle="collapse" href="#campaigner-pages" aria-expanded="true" aria-controls="general-pages">
                        <span class="menu-title">{{ trans("navigation.students report") }}</span>
                        <i class="menu-arrow"></i>
                        <i class="mdi mdi-assistant menu-icon"></i>
                    </a>
                    <div class="collapse show" id="campaigner-pages" style="">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('school-account-manager.general-quizzes-reports.branch.reports.class.levels') }}">{{ trans("navigation.Class Level Report") }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('school-account-manager.general-quizzes-reports.branch-level-report') }}">{{ trans("navigation.Branch Level Report") }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('school-account-manager.general-quizzes-reports.student.level.students') }}">{{ trans("navigation.student level report") }}</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item {{(request()->is('*/reports*'))?"active":""}}">
                <a class="nav-link" href="{{ route("school-account-manager.reports.instructor.sessions.attendance") }}">
                    <span class="menu-title">{{ trans('navigation.Instructor Attendance') }} </span>
                    <i class="mdi mdi-assistant menu-icon"></i>
                </a>
               </li>

        @elseif(in_array(auth()->user()->type,[\App\OurEdu\Users\UserEnums::SCHOOL_SUPERVISOR,\App\OurEdu\Users\UserEnums::SCHOOL_LEADER, \App\OurEdu\Users\UserEnums::ACADEMIC_COORDINATOR] ))
            @if(can('view-gradeClasses') | can('create-gradeClasses'))
              <li class="nav-item {{(request()->is('*/grade-classes*'))?"active":""}}">
                <a class="nav-link" href="{{ route('school-branch-supervisor.grade-classes.get.index') }}">
                    <span class="menu-title">{{ trans('navigation.Grade Classes') }}</span>
                    <i class="mdi mdi-assistant menu-icon"></i>
                </a>
              </li>
            @endif

            @if(can('view-subjectInstructors') | can('create-subject-instructors'))
                <li class="nav-item {{(request()->is('*/subject-instructors*') and !request()->has("deactivated"))?"active":""}}">
                    <a class="nav-link" href="{{ route('school-branch-supervisor.subject-instructors.get.school-instructor') }}">
                        <span class="menu-title">{{ trans('navigation.School Instructors') }}</span>
                        <i class="mdi mdi-assistant menu-icon"></i>
                    </a>
                </li>

                <li class="nav-item {{(request()->is('*/subject-instructors/all') and request()->has("deactivated"))?"active":""}}">
                    <a class="nav-link" href="{{ route('school-branch-supervisor.subject-instructors.get.school-instructor') }}?deactivated">
                        <span class="menu-title">{{ trans('navigation.Deactivated School Instructors') }}</span>
                        <i class="mdi mdi-assistant menu-icon"></i>
                    </a>
                </li>
            @endif

            @if(can('view-classrooms') | can('create-classrooms'))
                <li class="nav-item {{(request()->is('*/classrooms*'))?"active":""}}">
                    <a class="nav-link" href="{{ route('school-branch-supervisor.classrooms.get.index') }}">
                        <span class="menu-title">{{ trans('navigation.Classrooms') }}</span>
                        <i class="mdi mdi-assistant menu-icon"></i>
                    </a>
                </li>
            @endif

            @if(can('view-instructorRate'))
                <li class="nav-item {{(request()->is('*/instructors-rates*'))?"active":""}}">
                    <a class="nav-link" href="{{ route('school-branch-supervisor.instructors-rates.get.index') }}">
                        <span class="menu-title">{{trans('navigation.instructorRates')}}</span>
                        <i class="mdi mdi-assistant menu-icon"></i>

                    </a>
                </li>
            @endif

            @if(can('view-classrooms') | can('create-classrooms'))
                <li class="nav-item {{(request()->is('*/special-classrooms*'))?"active":""}}">
                    <a class="nav-link" href="{{ route('school-branch-supervisor.specialClassrooms.get.index') }}">
                        <span class="menu-title">{{ trans('navigation.SpecialClassrooms') }}</span>
                        <i class="mdi mdi-assistant menu-icon"></i>
                    </a>
                </li>
            @endif


            @if(can('view-students') | can('create-students'))
                <li class="nav-item {{(request()->is('*/students/all') and !request()->has("deactivated"))?"active":""}}">
                    <a class="nav-link" href="{{ route('school-branch-supervisor.students.get.index') }}">
                        <span class="menu-title">{{ trans('navigation.Students') }}</span>
                        <i class="mdi mdi-assistant menu-icon"></i>
                    </a>
                </li>

                <li class="nav-item {{(request()->is('*/students/all') and request()->has("deactivated"))?"active":""}}">
                    <a class="nav-link" href="{{ route('school-branch-supervisor.students.get.index') }}?deactivated">
                        <span class="menu-title">{{ trans('navigation.Deactivated Students') }}</span>
                        <i class="mdi mdi-assistant menu-icon"></i>
                    </a>
                </li>
            @endif

            @if(can('view-educationalSupervisor'))
                <li class="nav-item {{(request()->is('*/educational-supervisors'))?"active":""}}">
                    <a class="nav-link" href="{{ route('school-branch-supervisor.educational-supervisors.get.index') }}">
                        <span class="menu-title">{{ trans('navigation.Educational Supervisor') }}</span>
                        <i class="mdi mdi-assistant menu-icon"></i>
                    </a>
                </li>
            @endif

            <li class="nav-item {{(request()->is('*/preperation'))?"active":""}}">
                <a class="nav-link" href="{{ route('school-branch-supervisor.session.preparation.get.media.library') }}">
                    <span class="menu-title">{{ trans('navigation.Media Library') }}</span>
                    <i class="mdi mdi-assistant menu-icon"></i>
                </a>
            </li>

            @if(can('view-parents') | can('create-parents'))
                <li class="nav-item {{(request()->is('*/parents*'))?"active":""}}">
                    <a class="nav-link" href="{{ route('school-branch-supervisor.students.get.parents') }}">
                        <span class="menu-title">{{ trans('navigation.Parents') }}</span>
                        <i class="mdi mdi-assistant menu-icon"></i>
                    </a>
                </li>
            @endif
            <li class="nav-item {{Route::is('*reports.students.class.presence') ? 'active' : null}}">
                <a class="nav-link" data-toggle="collapse" href="#campaigner-pages" aria-expanded="true" aria-controls="general-pages">
                    <span class="menu-title">{{ trans('navigation.reports') }}</span>
                    <i class="mdi  mdi-arrow-bottom-left menu-icon"></i>
                </a>
                <div class="collapse show" id="campaigner-pages" style="">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('school-account-manager.reports.students.class.presence') }}">{{ trans('navigation.Students class Presence') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('school-account-manager.reports.students.subjects.presence') }}">{{ trans('navigation.Students Subjects Presence') }}</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item {{(request()->is('*/quizzes*'))?"active":""}}">
                <a class="nav-link" href="{{ route('school-branch-supervisor.quiz.index') }}">
                    <span class="menu-title">{{ trans('navigation.quizzes') }}</span>
                    <i class="mdi mdi-assistant menu-icon"></i>
                </a>
            </li>

            <li class="nav-item {{(request()->is('*/general-quizzes/*'))?"active":""}}">
                <a class="nav-link" href="{{ route('school-branch-supervisor.general-quizzes.index') }}">
                    <span class="menu-title">{{ trans('navigation.Quizzes Reports') }}</span>
                    <i class="mdi mdi-assistant menu-icon"></i>
                </a>
            </li>

            <li class="nav-item {{(request()->is('*/trashed/*'))?"active":""}}">
                <a class="nav-link" href="{{ route('school-branch-supervisor.classrooms.trashed') }}">
                    <span class="menu-title">{{ trans('navigation.trashed_classrooms_reports') }}</span>
                    <i class="mdi mdi-assistant menu-icon"></i>
                </a>
            </li>

            <li class="nav-item {{(request()->is('*/total-percentages-report'))?"active":""}} ">
                <a class="nav-link" href="{{ route('school-branch-supervisor.general-quizzes-reports.total.percentages.report') }}">
                    <span class="menu-title">{{ trans("navigation.total percentages report") }} </span>
                    <i class="mdi mdi-assistant menu-icon"></i>
                </a>
            </li>

            <li class="nav-item {{request()->is('*/branch-reports-*') ? 'active' : null}}">
                <a class="nav-link" data-toggle="collapse" href="#campaigner-pages" aria-expanded="true" aria-controls="general-pages">
                    <span class="menu-title">{{ trans("navigation.branches_reports") }}</span>
                    <i class="menu-arrow"></i>
                    <i class="mdi mdi-assistant menu-icon"></i>
                </a>
                <div class="collapse show" id="campaigner-pages" style="">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('school-branch-supervisor.general-quizzes-reports.branch.reports.instructor.levels') }}">{{ trans("navigation.instructor level report") }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('school-branch-supervisor.general-quizzes-reports.branch.reports.subject.levels') }}">{{ trans("navigation.subject level report") }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('school-branch-supervisor.general-quizzes-reports.branch.reports.skill.levels') }}">{{ trans("navigation.Skill Level Report") }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('school-branch-supervisor.general-quizzes-reports.question.percentage.report.index') }}">{{ trans("navigation.questions Percentages") }}</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item {{Route::is('*branch-level-report') ? 'active' : null}}">
                <a class="nav-link" data-toggle="collapse" href="#campaigner-pages" aria-expanded="true" aria-controls="general-pages">
                    <span class="menu-title">{{ trans("navigation.students report") }}</span>
                    <i class="menu-arrow"></i>
                    <i class="mdi mdi-assistant menu-icon"></i>
                </a>
                <div class="collapse show" id="campaigner-pages" style="">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('school-branch-supervisor.general-quizzes-reports.branch.reports.class.levels') }}">{{ trans("navigation.Class Level Report") }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('school-branch-supervisor.general-quizzes-reports.branch-level-report') }}">{{ trans("navigation.Branch Level Report") }}</a>
                        </li>
                        <li class="nav-item">
                                <a class="nav-link" href="{{ route('school-branch-supervisor.general-quizzes-reports.student.level.students') }}">{{ trans("navigation.student level report") }}</a>
                        </li>
                    </ul>
                </div>
            </li>

    @endif

    @if(auth()->user()->type == \App\OurEdu\Users\UserEnums::SCHOOL_ACCOUNT_MANAGER)
        <li class="nav-item {{(request()->is('*/managerReports*'))?"active":""}}">
            <a class="nav-link" href="{{ route('school-account-manager.manager-reports.user-attends') }}">
                <span class="menu-title">{{ trans('navigation.User Attends') }}</span>
                <i class="mdi mdi-assistant menu-icon"></i>
            </a>
        </li>
    @endif

        <li class="nav-item {{(request()->is('*/assessments'))?"active":""}}">
            <a class="nav-link" href="{{ route('school-branch-supervisor.assessor.assessments.index') }}">
                <span class="menu-title">{{ trans('navigation.assessments') }}</span>
                <i class="mdi mdi-assistant menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{(request()->is('*/assessments/reports'))?"active":""}}">
            <a class="nav-link" href="{{ route('school-branch-supervisor.result-viewers.assessments.index') }}">
                <span class="menu-title">{{ trans('navigation.assessments-report') }}</span>
                <i class="mdi mdi-assistant menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{(request()->is('*/assessee/assessments'))?"active":""}}">
            <a class="nav-link" href="{{ route('school-branch-supervisor.assessee.assessments.list') }}">
                <span class="menu-title">{{ trans('navigation.my_assessments') }}</span>
                <i class="mdi mdi-assistant menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{(request()->is('*/profiles*'))?"active":""}}">
            <a class="nav-link" href="{{ route('profile.school.account.profile.edit') }}">
                <span class="menu-title">{{ trans('navigation.profile') }}</span>
                <i class="mdi mdi-assistant menu-icon"></i>
            </a>
        </li>
    </ul>
</nav>