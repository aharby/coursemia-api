<div class="col-md-3 left_col" style="position:fixed;max-height: 200px;overflow-y: scroll; overflow-x: hidden">
    <div class="left_col scroll-view">
        <div class="navbar nav_title" style="border: 0;">
            <a href="{{ route('admin.dashboard') }}" class="site_title">
                <span><img src='/img/white.png' width="25%"> </span>
            </a>

        </div>

        {{--<div class="clearfix"></div>--}}

        {{--        <a class="btn btn-large btn-success" href="javascript:void(0);" onclick="javascript:introJs().start();">Show me how</a>--}}

        <!-- menu profile quick info -->
        {{--        <div class="profile clearfix">--}}
        {{--            <div class="profile_pic">--}}
        {{--                {!!  viewImage(auth()->user()->profile_picture,'small',null,['width'=>'75%']) !!}--}}
        {{--            </div>--}}
        {{--            <div class="profile_info">--}}
        {{--                <h2>{{ auth()->user()->name }}</h2>--}}
        {{--            </div>--}}
        {{--        </div>--}}


        <!-- /menu profile quick info -->

        <br/>

        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section" style="margin-top: 50px">

                <ul class="nav side-menu">

                </ul>

                <ul class="nav side-menu">
                    <li>
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fa fa-home" aria-hidden="true"></i>
                            {{ trans('app.Home') }}
                        </a>
                    </li>
                    <li>
                        <a>
                            <i class="fa fa-shopping-cart"></i> {{ trans('payment.payments')}}<span
                                class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li>
                                <a href="{{ route('admin.paymentReport.get.index') }}">
                                    <i class="fa fa-credit-card" aria-hidden="true"></i>
                                    {{ trans('app.top_quodrat_payment') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.payments.failed_transactions') }}">
                                    <i class="fa fa-cc-visa" aria-hidden="true"></i>
                                    {{ trans('payment.failed_transactions') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                    <ul class="nav side-menu">
                        <li><a><i class="fa fa-search"></i> {{ trans('app.Look Up')}}<span
                                    class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu">
                                <li>
                                    <a href="{{ route('admin.countries.get.index') }}">
                                        <i class="fa fa-flag" aria-hidden="true"></i>
                                        {{ trans('navigation.Countries') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.educationalSystems.get.index') }}">
                                        <i class="fa fa-assistive-listening-systems" aria-hidden="true"></i>
                                        {{ trans('navigation.Educational System') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.gradeClasses.get.index') }}">
                                        <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                                        {{ trans('navigation.Grade classes') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.grade.colors.index') }}">
                                        <i class="fa fa-paint-brush" aria-hidden="true"></i>
                                        {{ trans('navigation.Grade colors') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.schools.get.index') }}">
                                        <i class="fas fa-school" style="margin-right: 12px" aria-hidden="true"></i>
                                        {{ trans('navigation.Schools') }}
                                    </a>
                                </li>
                            </ul>

                    </ul>

                    <li>
                        <a href="{{ route('admin.options.get.index') }}">
                            <i class="fa fa-server" aria-hidden="true"></i>
                            {{ trans('navigation.Options') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.get.index') }}">
                            <i class="fa fa-users" aria-hidden="true"></i>
                            {{ trans('navigation.Users') }}
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.subjects.get.index') }}">
                            <div class="span6" data-step="2" data-intro="{{trans('introJs.This is the subject !!')}}"
                                 data-position='right' data-scrollTo='tooltip'>
                                <i class="fa fa-newspaper-o" aria-hidden="true"></i>
                                {{ trans('navigation.Subjects') }}
                            </div>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.subjectPackages.get.index') }}">
                            <div class="span6" data-step="3"
                                 data-intro="{{trans('introJs.This is the subject Packages!!')}}" data-position='right'
                                 data-scrollTo='tooltip'>
                                <i class="	fa fa-th-large" aria-hidden="true"></i>
                                {{ trans('navigation.Subject Packages') }}
                            </div>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.subject-structure.get.SubjectStructureLogs') }}">
                            <i class="fa fa-folder" aria-hidden="true"></i>
                            {{ trans('navigation.Subject Structure Logs') }}
                        </a>
                    </li>


                    <li>
                        <a href="{{ route('admin.feedbacks.get.index') }}">
                            <i class="fa fa-user-secret" aria-hidden="true"></i>
                            {{ trans('navigation.Feedback') }}
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.courses.get.index') }}">
                            <div class="span6" data-step="4" data-intro="{{trans('introJs.This is the Courses !!')}}"
                                 data-position='right' data-scrollTo='tooltip'>
                                <i class="fas fa-chalkboard" style="margin-right: 12px" aria-hidden="true"></i>
                                {{ trans('navigation.Courses') }}
                            </div>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.psychological_tests.get.index') }}">
                            <i class="fa fa-book" aria-hidden="true"></i>
                            {{ trans('navigation.Psychological tests') }}
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.subjects.get.student-grades') }}">
                            <i class="fas fa-user-graduate" style="margin-right: 12px" aria-hidden="true"></i>
                            {{ trans('navigation.Student grades') }}
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.liveSessions.get.index') }}">
                            <div class="span6" data-step="5"
                                 data-intro="{{trans('introJs.This is the Live Session !!')}}" data-position='right'>
                                <i class="fab fa-youtube" style="margin-right: 12px" aria-hidden="true"></i>
                                {{ trans('navigation.Live sessions') }}
                            </div>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.vcr_schedules.get.index') }}">
                            <i class="glyphicon glyphicon-facetime-video" aria-hidden="true"></i>
                            {{ trans('navigation.VCR_schedules') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.vcr-sessions.vcr-sessions.subjects') }}">
                            <i class="glyphicon glyphicon-facetime-video" aria-hidden="true"></i>
                            {{ trans('navigation.VCRStudents') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.configs.get.edit') }}">
                            <i class="fa fa-cog" aria-hidden="true"></i>
                            {{ trans('navigation.Configs') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.app.versions.get.edit') }}">
                            <i class="fa fa-cog" aria-hidden="true"></i>
                            {{ trans('navigation.App versions') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.contact.get.index') }}">
                            <i class="fa fa-phone" aria-hidden="true"></i>
                            {{ trans('navigation.Contact') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.staticPages.get.index') }}">
                            <i class="fas fa-pager" style="margin-right: 12px" aria-hidden="true"></i>
                            {{ trans('navigation.Static Pages') }}
                        </a>
                    </li>
                    {{--                    <li>--}}
                    {{--                        <a href="{{ route('admin.certificates.thanking.index') }}">--}}
                    {{--                            <i class="fas fa-certificate" style="margin-right: 12px" aria-hidden="true"></i>--}}
                    {{--                            {{ trans('navigation.Thanking Certificates') }}--}}
                    {{--                        </a>--}}
                    {{--                    </li>--}}

                </ul>

                <ul class="nav side-menu">
                    <li>
                        <a><i class="fas fa-school" style="margin-right: 12px"></i>{{ trans('app.School Accounts') }}
                            <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li>
                                <a href="{{ route('admin.school-requests.get.index') }}">
                                    {{ trans('app.School Requests') }}
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.school-accounts.get.index') }}">
                                    {{ trans('app.School Accounts') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.school-account-branches.get.index') }}">
                                    {{ trans('app.School Account Branches') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>

                <ul class="nav side-menu">
                    <li><a><i class="fa fa-bar-chart"></i> {{ trans('navigation.Reports')}}<span
                                class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li>
                                <a href="{{ route('admin.questionReports.get.index') }}">
                                    <i class="fa fa-file-text" aria-hidden="true"></i>
                                    {{ trans('navigation.Question Reports') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.instructors.get.index') }}">
                                    <i class="fa fa-film" aria-hidden="true"></i>
                                    {{ trans('navigation.Instructor') }}
                                </a>
                            </li>
                        </ul>

                </ul>
                <ul class="nav side-menu">
                    <li><a><i class="fa fa-bar-chart"></i> {{ trans('navigation.Student Reports')}}<span
                                class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li>
                                <a href="{{ route('admin.reports.get.index') }}">
                                    <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                                    {{ trans('navigation.Student Reports') }}
                                </a>
                            </li>
                        </ul>

                </ul>

                <ul class="nav side-menu">
                    <li>
                        <a><i class="fa fa-tasks"></i>{{ trans('navigation.Tasks') }}<span
                                class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li>
                                <a href="{{ route('admin.tasks.get.content.author.tasks') }}">
                                    <i class="fa fa-book" aria-hidden="true"></i>
                                    {{ trans('navigation.Content Author Tasks') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>


            </div>

            {{--            <div class="menu_section">--}}
            {{--                <h3>{{ __('Txt_text.') }}</h3>--}}

            {{--                <ul class="nav side-menu">--}}
            {{--                    <li>--}}
            {{--                        <a>--}}
            {{--                            <i class="fa fa-list"></i>--}}
            {{--                            {{ __('Txt text') }}--}}
            {{--                            <span class="fa fa-chevron-down"></span>--}}
            {{--                        </a>--}}
            {{--                        <ul class="nav child_menu">--}}
            {{--                            <li>--}}
            {{--                                <a href="#">--}}
            {{--                                    {{ __('app,N') }}--}}
            {{--                                </a>--}}
            {{--                            </li>--}}
            {{--                            <li>--}}
            {{--                                <a href="#">--}}
            {{--                                    {{ __('Txt_text.') }}--}}
            {{--                                </a>--}}
            {{--                            </li>--}}
            {{--                        </ul>--}}
            {{--                    </li>--}}
            {{--                </ul>--}}
            {{--            </div>--}}
        </div>
        <!-- /sidebar menu -->
    </div>
</div>
{{--<script src="{{asset('js/main.js')}}" type="text/javascript" ></script>--}}

