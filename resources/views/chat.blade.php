<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Danilo Aquino</title>

    <!-- Font Awesome -->
    <script src="https://use.fontawesome.com/9b85ec9ead.js"></script>
    <!-- Bootstrap -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!--     HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif] -->

    <style>
        @import url('https://fonts.googleapis.com/css?family=Roboto');

        .navbar {
            border: none;
            font-family: 'Roboto', sans-serif;
        }

        #site-title {
            padding-top: 100px;
            width: 100%;
            margin: auto;
            text-align: center;
            font-family: 'Roboto', sans-serif;
        }

        body {
            margin: 0;
            background-color: rgb(238, 238, 238);
            font-family: 'Helvetica Neue', 'Roboto', sans-serif;
            height: 800px;
        }



        .gif-container {
            max-width: 150px;
            height: 100px;
            border-radius: 4px;
            display: flex;
            float: left;
        }

        .bubble {
            margin-top: 5px;
            width: 75%;
            border-radius: 15px;
            padding: 10px 20px 10px 20px;
            display: flex;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.25);
        }

        .user-input {
            float: right;
            background-color: #1289fe;
            color: #fff;
        }

        .bot-output {
            float: left;
            background-color: #e5e5ea;
        color: #000;
        }

    </style>
</head>
<body>
<!-- Start of Navigation Bar -->
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><i class="fa fa-code"></i> Danilo Aquino</a>
        </div>
    </div>
</div>
<!-- End of Navigation Bar -->

<!-- Site Title -->
<div id="site-title">
    <h1>Chat App <i class="fa fa-commenting-o"></i><h1>
</div>

<!-- Chat Container -->
<div id="container">
    <div id="main-container">
{{--           <h5>Responds to:</h5>--}}
                <ul>
                    @if(isset($rooms))
                        @foreach($rooms as $room)
                            <li><a href="{{route("chatTest.roomMessages" , ['room' => $room])}}">{{$room}}</a></li>
                        @endforeach
                    @endif
                </ul>
    </div>

    <!-- Chat Controller -->
    <div id="controller">
        <textarea id="textbox" class="form-control" rows="2" placeholder="Enter your message here"></textarea>
        <div class="checkbox"><label><input checked type="checkbox" id="enter"/>Send on enter</label>
            <button id="send" class="btn btn-primary">Send</button>
        </div>
    </div>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

</body>
</html>



