<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <link rel="stylesheet" href="https://awwapp.com/static/widget/css/toolbar_style.css">
    <title>Document</title>
</head>
<body>
11577247-133b-4620-8e9c-4634ba59a1b8
<div id="aww-wrapper" class="aww" style="height: 600px;"></div>


<script src="https://awwapp.com/static/widget/js/aww3.min.js"></script>

<script type="text/javascript">
    var aww = new AwwBoard('#aww-wrapper', {
        apiKey: '11577247-133b-4620-8e9c-4634ba59a1b8',
        multiPage: true,
        sendUserPointer: true,
        showUserPointers: true,
        boardLink: 'ourEdu-5aj5342-56tz-uhjk-9874',
        autojoin: true
    });
    $.ajax({
        'method': 'GET',
        'url': 'https://awwapp.com/static/widget/sample_toolbar.html'
    }).done(function(res, status) {
        $('#aww-wrapper').append(res);
        initToolbar();
    });
</script>
<script src="https://awwapp.com/static/widget/sample_toolbar.js"></script>

{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>--}}
{{--<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>--}}


</body>
</html>
