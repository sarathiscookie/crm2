<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags always come first -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>TP-CRM : @yield('title')</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/jquery-ui.min.css">
{{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

    <!-- Fonts -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

    @yield('style')

    <style>
        body {
            font-family: 'Lato';
        }

        .fa-btn {
            margin-right: 6px;
        }

        .navbar a {
            color: #FFF !important;
        }
    </style>
</head>
<body id="app-layout">
@include('includes.header')

    <div class="container">
    @yield('content')
    </div>
    @yield('contents')

<!-- JavaScripts -->
<script src="/assets/js/jquery.min.js"></script>
<script src="/assets/js/jquery-ui.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="/assets/js/bootstrap.min.js"></script>

{{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/js/bootstrap.min.js" integrity="sha384-vZ2WRJMwsjRMW/8U7i6PWi6AlO1L79snBrmgiDpgIWJ82z8eA5lenwvxbMV1PAh7" crossorigin="anonymous"></script>--}}
{{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
<script>
    //Header Nav bar Search
    $('#searchkey').keyup( function () {
        var val = $(this).val();
        if (val.length >= 2) {
            $('.srch-loader').toggleClass('invisible');
            $.get("/search", {key: val})
                    .done(function (data) {
                        $('.srch-loader').toggleClass('invisible');
                        if (data.result != '') {
                            $('#navSrchBox').html(data.result).show();
                        }
                        else {
                            $('#navSrchBox').html('<div class="alert alert-danger" role=alert><span>No results</span></div>').show();
                        }
                    });
        }
        else {
            $("#navSrchBox").html('').hide();
        }
    });

    //Toggle search results when clicking inside input holds previous keyword
    $('#searchkey').click( function () {
        if($("#navSrchBox").html()!='' && $("#navSrchBox").css('display') == 'none') {
            $('#navSrchBox').show();
        }
    });

    //Toggle search container display on page click
    $(document).mouseup(function (e)
    {
        var container = $("#navSrchBox");
        // if the target of the click isn't the container...nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            container.hide();
        }
    });
</script>
@stack('script')
</body>
</html>
