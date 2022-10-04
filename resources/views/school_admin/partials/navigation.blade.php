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
        @if(auth()->user()->type == \App\OurEdu\Users\UserEnums::SCHOOL_ADMIN)
            <li class="nav-item {{(request()->is('*/school-account-branches*'))?"active":""}}">
                <a class="nav-link" href="{{ route('school-admin.school-account-branches.get.index') }}">
                    <span class="menu-title">{{ trans('navigation.School Branches') }}</span>
                    <i class="mdi mdi-assistant menu-icon"></i>
                </a>
            </li>



            <li class="nav-item {{(request()->is('*/media/library'))?"active":""}}">
                <a class="nav-link" href="{{ route('school-admin.session.preparation.get.media.library') }}">
                    <span class="menu-title">{{ trans('navigation.Media Library') }}</span>
                    <i class="mdi mdi-assistant menu-icon"></i>
                </a>
            </li>

            <li class="nav-item {{(preg_match("/(\/general-quizzes\/)((?!formativeTests|formative-tests-reports).)*$/",request()->getRequestUri()))?"active":""}}">
                <a class="nav-link" href="{{ route('school-admin.general-quizzes.index') }}">
                    <span class="menu-title">{{ trans('navigation.Quizzes Reports') }}</span>
                    <i class="mdi mdi-assistant menu-icon"></i>
                </a>
            </li>

            <li class="nav-item {{(preg_match("/(formative-test\/|formativeTests)/",request()->getRequestUri()))?"active":""}}">
                <a class="nav-link" href="{{ route('school-admin.formative-test.index') }}">
                    <span class="menu-title">{{ trans('navigation.Formative test') }}</span>
                    <i class="mdi mdi-assistant menu-icon"></i>
                </a>
            </li>

            <li class="nav-item {{(preg_match("/(formative-tests-reports)/",request()->getRequestUri()))?"active":""}}">
                <a class="nav-link" href="{{ route('school-admin.general-quizzes-reports.formative-tests-reports.index') }}">
                    <span class="menu-title">{{ trans('navigation.formative_test_report') }}</span>
                    <i class="mdi mdi-assistant menu-icon"></i>
                </a>
            </li>

            <li class="nav-item {{(request()->is('*/total-percentages-reports*'))?"active":""}} ">
                <a class="nav-link" href="{{ route('school-admin.general-quizzes-reports.total-percentages-reports.get.index') }}">
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
                            <a class="nav-link" href="{{ route('school-admin.general-quizzes-reports.branch-reports.instructor.levels') }}">{{ trans("navigation.instructor level report") }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('school-admin.general-quizzes-reports.branch-reports.subject.levels') }}">{{ trans("navigation.subject level report") }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('school-admin.general-quizzes-reports.branch-reports.skill.levels') }}">{{ trans("navigation.Skill Level Report") }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('school-admin.general-quizzes-reports.branch-reports.question.percentage.report.index') }}">{{ trans("navigation.questions Percentages") }}</a>
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
                            <a class="nav-link" href="{{ route('school-admin.general-quizzes-reports.branch.reports.class.levels') }}">{{ trans("navigation.Class Level Report") }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('school-admin.general-quizzes-reports.branch-level-report') }}">{{ trans("navigation.Branch Level Report") }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('school-admin.general-quizzes-reports.student.level.students') }}">{{ trans("navigation.student level report") }}</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item {{(request()->is('*/assessments/reports'))?"active":""}}">
            <a class="nav-link" href="{{ route('school-admin.assessments.index') }}">
                <span class="menu-title">{{ trans('navigation.assessments-report') }}</span>
                <i class="mdi mdi-assistant menu-icon"></i>
            </a>
        </li>


            <li class="nav-item {{(request()->is('*/reports*'))?"active":""}}">
            <a class="nav-link" href="{{ route("school-admin.reports.instructor.sessions.attendance") }}">
                <span class="menu-title">{{ trans('navigation.Instructor Attendance') }} </span>
                <i class="mdi mdi-assistant menu-icon"></i>
            </a>
            </li>

            <li class="nav-item {{(request()->is('*/user-attends*'))?"active":""}}">
            <a class="nav-link" href="{{ route('school-admin.attendance-reports.user-attends') }}">
                <span class="menu-title">{{ trans('navigation.User Attends') }}</span>
                <i class="mdi mdi-assistant menu-icon"></i>
            </a>
        </li>

    @endif

        <li class="nav-item {{(request()->is('*/profile*'))?"active":""}}">
            <a class="nav-link" href="{{ route('profile.school.account.profile.edit') }}">
                <span class="menu-title">{{ trans('navigation.profile') }}</span>
                <i class="mdi mdi-assistant menu-icon"></i>
            </a>
        </li>
    </ul>
</nav>

