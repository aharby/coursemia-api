{{--<style>--}}
    {{--html, body {--}}
        {{--background-color: #fff;--}}
        {{--color: #636b6f;--}}
        {{--font-family: 'Raleway', sans-serif;--}}
        {{--font-weight: 100;--}}
        {{--height: 100vh;--}}
        {{--margin: 0;--}}
    {{--}--}}

    {{--.full-height {--}}
        {{--height: 100vh;--}}
    {{--}--}}

    {{--.flex-center {--}}
        {{--align-items: center;--}}
        {{--display: flex;--}}
        {{--justify-content: center;--}}
    {{--}--}}

    {{--.position-ref {--}}
        {{--position: relative;--}}
    {{--}--}}

    {{--.top-right {--}}
        {{--position: absolute;--}}
        {{--right: 10px;--}}
        {{--top: 18px;--}}
    {{--}--}}

    {{--.content {--}}
        {{--text-align: center;--}}
    {{--}--}}

    {{--.title {--}}
        {{--font-size: 84px;--}}
    {{--}--}}

    {{--.links > a {--}}
        {{--color: #636b6f;--}}
        {{--padding: 0 25px;--}}
        {{--font-size: 12px;--}}
        {{--font-weight: 600;--}}
        {{--letter-spacing: .1rem;--}}
        {{--text-decoration: none;--}}
        {{--text-transform: uppercase;--}}
    {{--}--}}

    {{--.footer {--}}
        {{--position:fixed;--}}
        {{--width:100%;--}}
        {{--height:20px;--}}
        {{--padding:5px;--}}
        {{--bottom:0px;--}}
        {{--font-size: smaller;--}}
    {{--}--}}

    {{--.m-b-md {--}}
        {{--margin-bottom: 30px;--}}
    {{--}--}}

    {{--.dropbtn {--}}
        {{--background-color: #4176af;--}}
        {{--color: white;--}}
        {{--padding: 15px;--}}
        {{--font-size: 15px;--}}
        {{--border: none;--}}
    {{--}--}}

    {{--.dropdown {--}}
        {{--position: relative;--}}
        {{--display: inline-block;--}}
    {{--}--}}

    {{--.dropdown-content {--}}
        {{--display: none;--}}
        {{--position: absolute;--}}
        {{--background-color: #f1f1f1;--}}
        {{--min-width: 160px;--}}
        {{--box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);--}}
        {{--z-index: 1;--}}
    {{--}--}}

    {{--.dropdown-content a {--}}
        {{--color: black;--}}
        {{--padding: 12px 16px;--}}
        {{--text-decoration: none;--}}
        {{--display: block;--}}
    {{--}--}}

    {{--.dropdown-content a:hover {background-color: #ddd;}--}}

    {{--.dropdown:hover .dropdown-content {display: block;}--}}

    {{--.dropdown:hover .dropbtn {background-color: #3e8e41;}--}}
{{--</style>--}}

{{--<div class="dropdown">--}}
    {{--<button class="dropbtn">language</button>--}}
    {{--<div class="dropdown-content">--}}
        {{--@foreach(LaravelLocalization::getSupportedLocales() as $key => $value)--}}
            {{--<a href="{{LaravelLocalization::getLocalizedURL($key)}}">{{$value['native']}}"</a>--}}
        {{--@endforeach--}}
    {{--</div>--}}
{{--</div>--}}
<div class="dropdown dropdown-c pull-right language">
    <a href="#" class="logged-user" data-toggle="dropdown">
        <span>{{ucfirst(lang())}}</span>
        <i class="fa fa-angle-down"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right">
        <nav class="nav">
            @foreach(languages() as $key=>$lang)
                <li>
                <a href="{{urlLang(url()->full(),lang(),$key)}}" class="nav-link">{{$lang}}</a>
                </li>
            @endforeach
        </nav>
    </div>
    <!-- dropdown-menu -->
</div>

{{--</div>--}}
