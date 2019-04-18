
    <!DOCTYPE html>
    <html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{!! Session::token() !!}">

        <title>{{ get_app_name() }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Fonts -->
        <link rel="dns-prefetch" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">


        <!-- Bootstrap core CSS -->
        <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

        <!-- Custom fonts for this template -->
        <link href="{{ asset('my-tools/fontawesome-free-5.2.0-web/css/all.min.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('my-tools/font-awesome-4.7.0/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">

        <!-- Custom styles for this template -->
        <link href="{{ asset('css/agency-'.app()->getLocale().'.css') }}" rel="stylesheet">

        <!--________________________________DataTable Style__________________________________-->
        <link href="{{ asset('DataTable/datatables.min-'.app()->getLocale().'.css') }}" rel="stylesheet">
        <link href="{{ asset('DataTable/jquery.dataTables.min.css') }}" rel="stylesheet">

        <!--__________________________________Panel Style__________________________________-->
        <!-- SIMPLE LINE ICONS-->
        <link href="{{ asset('my-tools\panel\css\simple-line-icons.css') }}" rel="stylesheet">
        <!-- ANIMATE.CSS-->
        <link href="{{ asset('my-tools\panel\css\animate.min.css') }}" rel="stylesheet">
        <!-- WHIRL (spinners)-->
        <link href="{{ asset('my-tools\panel\css\whirl.css') }}" rel="stylesheet">
        <link href="{{ asset('my-tools\panel\css\panel-'.app()->getLocale().'.css') }}" rel="stylesheet">

        <!-- Sweet Modals Style -->
        <link href="{{ asset('my-tools/sweet-modals/dev/jquery.sweet-modal-'.app()->getLocale().'.css') }}" rel="stylesheet">

        <!--__________________________________Loading Page Style__________________________________-->
        <link href="{{ asset('my-tools/loading-page/css/style.css') }}" rel="stylesheet">

        <!--__________________________________Special Style__________________________________-->
        <link href="{{ asset('css/special-'.app()->getLocale().'.css') }}" rel="stylesheet">
        
    </head>
    <body id="page-top">
        <div class="loading">Loading&#8230;</div>
        <input type="hidden" id="is_guest" value="{{ Auth::guest() }}">
    