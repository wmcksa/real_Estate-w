<!DOCTYPE html>
<!--[if lt IE 7 ]>
<html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>
<html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>
<html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html class="no-js" lang="en" @if(session()->get('rtl') == 1) dir="rtl" @endif >
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.seo')

    <link rel="stylesheet" type="text/css" href="{{asset($themeTrue.'css/bootstrap.min.css')}}"/>
    <link href="{{asset('assets/global/css/select2.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset($themeTrue.'css/animate.css')}}">
    <link rel="stylesheet" href="{{asset('assets/global/css/owl.carousel.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/global/css/owl.theme.default.min.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset($themeTrue.'css/range-slider.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset($themeTrue.'css/fancybox.css')}}">

    @stack('css-lib')

    <link rel="stylesheet" type="text/css" href="{{asset($themeTrue.'css/style.css')}}">
    <script src="{{asset($themeTrue.'js/fontawesomepro.js')}}"></script>
    <script src="{{asset($themeTrue.'js/modernizr.custom.js')}}"></script>

    @stack('style')

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script type="application/javascript" src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script type="application/javascript" src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body @if(session()->get('rtl') == 1) class="rtl" @endif>

<!------- Nav + Content ---------->
<div class="bottom-nav fixed-bottom d-lg-none">
    <div class="link-item {{menuActive(['user.propertyMarket'])}}">
        <a href="{{ route('user.propertyMarket', 'investment-properties') }}">
            <i class="fal fa-project-diagram" aria-hidden="true"></i>
            <span>@lang('Invest')</span>
        </a>
    </div>

    <div class="link-item {{menuActive(['user.addFund', 'user.addFund.confirm'])}}">
        <a href="{{ route('user.addFund') }}">
            <i class="fal fa-funnel-dollar" aria-hidden="true"></i>
            <span>@lang('Deposit')</span>
        </a>
    </div>

    <div class="link-item {{menuActive('user.home')}}">
        <a href="{{ route('user.home') }}">
            <i class="fal fa-home-lg-alt"></i>
            <span>@lang('Home')</span>
        </a>
    </div>

    <div class="link-item {{menuActive(['user.payout.money','user.payout.preview'])}}">
        <a href="{{ route('user.payout.money') }}">
            <i class="fal fa-hand-holding-usd" aria-hidden="true"></i>
            <span>@lang('Withdraw')</span>
        </a>
    </div>
    <div class="link-item {{menuActive(['user.profile'])}}">
        <button onclick="toggleSideMenu()">
            <i class="fal fa-ellipsis-v-alt"></i>
            <span>@lang('Menu')</span>
        </button>
    </div>
</div>

<div class="wrapper">
    <!------ sidebar ------->
    @include($theme.'partials.sidebar')

    <!-- content -->
    <div id="content">
        <div class="overlay">
            <!-- navbar -->
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid">
                    <a class="navbar-brand d-lg-none" href="{{route('home')}}">
                        <img src="{{getFile(config('location.logoIcon.path').'logo.png')}}"
                             alt="{{config('basic.site_title')}}">
                    </a>
                    <button class="sidebar-toggler d-none d-lg-block" onclick="toggleSideMenu()">
                        <i class="far fa-bars"></i>
                    </button>
                    <!-- navbar text -->
                    <span class="navbar-text" id="pushNotificationArea">
                            <!-- notification panel -->
                            @if(config('basic.push_notification') == 1)
                            @include($theme.'partials.pushNotify')
                        @endif
                        <!-- User panel -->
                        @include($theme.'partials.userDropdown')
                        </span>
                </div>
            </nav>
            @yield('content')
        </div>
    </div>
</div>

@stack('loadModal')
<script src="{{asset($themeTrue.'js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset($themeTrue.'js/jquery.min.js')}}"></script>
<script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
<script src="{{asset('assets/global/js/owl.carousel.min.js')}}"></script>
<script src="{{asset($themeTrue.'js/range-slider.min.js')}}"></script>
<script src="{{asset($themeTrue.'js/socialSharing.js')}}"></script>
<script src="{{asset($themeTrue.'js/fancybox.umd.js')}}"></script>
<script src="{{asset($themeTrue.'js/apexcharts.min.js')}}"></script>

@stack('extra-js')

<script src="{{asset('assets/global/js/notiflix-aio-2.7.0.min.js')}}"></script>
<script src="{{asset('assets/global/js/pusher.min.js')}}"></script>
<script src="{{asset('assets/global/js/vue.min.js')}}"></script>
<script src="{{asset('assets/global/js/axios.min.js')}}"></script>
<!-- custom script -->
<script src="{{asset($themeTrue.'js/script.js')}}"></script>


<script>
    'use strict';

    $(".card-boxes").owlCarousel({
        loop: true,
        margin: -25,
        rtl: false,
        nav: false,
        dots: false,
        autoplay: false,
        autoplayTimeout: 3000,
        responsive: {
            0: {
                items: 1,
            },
            576: {
                items: 2,
            },
        },
    });

    // dashboard sidebar
    window.onload = function () {
        var el = document.getElementById('sidebarCollapse');
        if (el == null) {
            return 0;
        } else {

            el.addEventListener("click", () => {
                document.getElementById("sidebar").classList.toggle("active");
                document.getElementById("content").classList.toggle("active");
            });
        }

        // for datepicker
        $(function () {
            $("#datepicker").datepicker({
                dateFormat: "yy-mm-dd"
            });
            $("#salutation").selectmenu();
        });
    }

    @if(config('basic.push_notification') == 1)
        let pushNotificationArea = new Vue({
        el: "#pushNotificationArea",
        data: {
            items: [],
        },
        mounted() {
            this.getNotifications();
            this.pushNewItem();
        },
        methods: {
            getNotifications() {
                let app = this;
                axios.get("{{ route('user.push.notification.show') }}")
                    .then(function (res) {
                        app.items = res.data;
                    })
            },
            readAt(id, link) {
                let app = this;
                let url = "{{ route('user.push.notification.readAt', 0) }}";
                url = url.replace(/.$/, id);
                axios.get(url)
                    .then(function (res) {
                        if (res.status) {
                            app.getNotifications();
                            if (link != '#') {
                                window.location.href = link
                            }
                        }
                    })
            },
            readAll() {
                let app = this;
                let url = "{{ route('user.push.notification.readAll') }}";
                axios.get(url)
                    .then(function (res) {
                        if (res.status) {
                            app.items = [];
                        }
                    })
            },
            pushNewItem() {
                let app = this;
                let pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
                    encrypted: true,
                    cluster: "{{ env('PUSHER_APP_CLUSTER') }}"
                });
                let channel = pusher.subscribe('user-notification.' + "{{ Auth::id() }}");
                channel.bind('App\\Events\\UserNotification', function (data) {
                    app.items.unshift(data.message);
                });
                channel.bind('App\\Events\\UpdateUserNotification', function (data) {
                    app.getNotifications();
                });
            }
        }
    });
    @endif

</script>

@stack('script')


@if (session()->has('success'))
    <script>
        Notiflix.Notify.Success("@lang(session('success'))");
    </script>
@endif

@if (session()->has('error'))
    <script>
        Notiflix.Notify.Failure("@lang(session('error'))");
    </script>
@endif

@if (session()->has('warning'))
    <script>
        Notiflix.Notify.Warning("@lang(session('warning'))");
    </script>
@endif

@include('plugins')

</body>
</html>
