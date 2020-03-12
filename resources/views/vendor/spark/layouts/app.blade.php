<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{--<title>@yield('title', config('app.name'))</title>--}}
    <title>{{isset($page['title']) ? $page['title'] : 'Link Slick'}}</title>

    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600' rel='stylesheet' type='text/css'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'>

    <!-- CSS -->
    <link href="/css/sweetalert.css" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="/css/custom.css?_token={{uniqid()}}" rel="stylesheet">

    <!-- Scripts -->
    @yield('scripts', '')

    <!-- Global Spark Object -->
    <script>
        window.Spark = <?php echo json_encode(array_merge(
            Spark::scriptVariables(), []
        )); ?>;
    </script>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-M5LG4GR');</script>
    <!-- End Google Tag Manager -->
    <script type="application/javascript" src="{{env('APP_URL')}}/js/jquery-2.2.4.min.js"></script>
    <script type="application/javascript" src="{{env('APP_URL')}}/js/notify.min.js"></script>
    <script type="application/javascript">
        var appUrl = '{{env("APP_URL")}}';
        var apiUrl = '{{env("API_URL")}}';
        $.ajaxSetup({
            headers: {'X-CSRF-Token': '{{ csrf_token() }}'}
        });
    </script>
</head>
<body class="with-navbar">
    <div id="spark-app" v-cloak>
        <!-- Navigation -->
        @if (Auth::check())
            @include('spark::nav.user')
        @else
            @include('spark::nav.guest')
        @endif

        <!-- Main Content -->
        @yield('content')

        <!-- Application Level Modals -->
        @if (Auth::check())
            @include('spark::modals.notifications')
            @include('spark::modals.support')
            @include('spark::modals.session-expired')
        @endif
    </div>

    <!-- JavaScript -->
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="/js/sweetalert.min.js"></script>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M5LG4GR"
                      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <script>
        $(function(){
            $.ajax({
                url: apiUrl + '/get/total/visit',
                method: 'GET',
                data: {},
                success: function (response) {
                    var res = JSON.parse(response);
                    var html = '<div class="result">'+Math.ceil(res.percentVisit)+'% Used</div>' +
                        '<div class="progress">' +
                        '<div class="progress-bar" style="width: '+Math.ceil(res.percentVisit)+'%"></div>' +
                        '</div>' +
                        '<div class="result">'+res.total_visit+' out of '+res.planInfo.visit+'</div>';
                    $('#nav-progressBar').html(html);
                    console.log(res)
                }
            });
        })
    </script>
</body>
</html>
