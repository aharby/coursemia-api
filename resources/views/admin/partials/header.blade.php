
@if(app()->getLocale() == 'ar')
    <link rel="stylesheet" href="{{asset('css/introJs.css')}}">
    <link rel="stylesheet" href="{{asset('css/introJs_rtl.css')}}">
@else
    <link rel="stylesheet" href="{{asset('css/introJs.css')}}">
@endif

{{--<link rel="stylesheet" href="{{asset('css/introJs.css')}}">--}}
<div class="top_nav">
    <div class="nav_menu">
        <nav>
            <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>
            @if(request()->is(['*/dashboard*','*/subjects*','*/subject-packages*', '*/courses*', '*/live-sessions*']))
                <a  href="javascript:void(0);" onclick="javascript:introJs().start();">
                    <i class="fa fa-info-circle fa-2x" style="margin-top: 16px"></i>
                </a>
            @endif

            <ul class="nav navbar-nav navbar-right">
                <li class="">
                    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown"
                       aria-expanded="false">
                        <div class="span6" data-step="1" data-intro="{{trans('introJs.This is the profile !!')}}"  data-position='right' >
                            {!!  viewImage(auth()->user()->profile_picture,'small') !!}
                            {{ auth()->user()->name }}
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-usermenu pull-right">
                        <li>
                            <a href="{{ route('profile.admin.get.edit') }}">
                                <i class="fa fa-user-circle pull-right"></i> {{ trans('header.My Profile') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile.get.logout') }}">
                                <i class="fas fa-sign-out-alt pull-right"></i> {{ trans('header.Logout') }}
                            </a>
                        </li>
                    </ul>
                </li>
                @include('partials.langSwitch')
            </ul>
        </nav>
    </div>
</div>
<script src="{{asset('js/introJs.js')}}" type="text/javascript" ></script>
