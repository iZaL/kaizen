<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ ! empty($title) ? $title . ' - ' : '' }} Kuwaitii.com</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @section('style')

    {{ HTML::style('css/bootstrap.min.css') }}
    {{ HTML::style('css/font-awesome.min.css') }}

    @if ( App::getLocale() == 'ar')
        {{ HTML::style('css/bootstrap-rtl.min.css') }}
    @endif

    {{ HTML::style('css/custom.css') }}

    @if ( App::getLocale() == 'en')
        {{ HTML::style('css/custom-en.css') }}
    @endif

    @show

</head>
<body>
    <div class="container">
        <!-- header -->
        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-12">
                <a href="/">{{ HTML::image('images/Logo.png','kaizen',array('class'=>'img-responsive')) }}</a>
            </div>
            <div class="col-md-7 col-sm-7 visible-lg visible-md visible-sm top30">
                @include('site.partials.login')
            </div>
            <div class="col-md-1 col-sm-1  visible-lg visible-md visible-sm top30">
                @include('site.partials.locale')
            </div>
        </div>
        <div class="row">
            @yield('nav')
        </div>
        <div class="row visible-xs">
            <div class="col-xs-2">
                @include('site.partials.locale')
            </div>
            <div class="col-xs-10">
                @include('site.partials.login')
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                @include('site.partials.notifications')
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                @section('slider')
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                @section('ads')
            </div>
        </div>
        <div class="row">
            <!-- main content division -->
            <div class="col-md-8">
                @yield('content')
            </div>
            <!-- end of main content-->
        </div>
        <div class="row">
            @include('site.partials.footer')
        </div>

    </div>
    <!--end of container-->

    <!-- Javascript -->
    @section('script')

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>

    @show

</body>
</html>