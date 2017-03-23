<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="fragment" content="!">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} | {{ config('app.slogan') }}</title>
    <meta name="application-name" content="{{ config('app.name') }} - {{ config('app.slogan') }}"/>
    <meta name="description"
          content="Compress and optimize your images for web use, We can compress your images up to 98% for less bandwidth and better user experience.">

    <!-- Google Tag Manager -->
    <script>(function (w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(), event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-PN5NSLG');</script>
    <!-- End Google Tag Manager -->

    @include('layouts.icons')

    <base href="/">

    <style>
        body {
            background-color: white;
        }

        .card {
            background-color: white;
        }

        .app-header .brand {
            height: 100px;
            display: block;
            text-align: center;
            max-width: 310px;
            padding: .75em 0
        }

        .app-header .brand img {
            height: 100%
        }

        .dropzone-drag-visual {
            opacity: 0
        }
    </style>

    <!-- Scripts -->
    <script>
        window.SMI = {!! json_encode([
            'state' => [
                'user' => auth()->user()
            ],
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PN5NSLG"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->

<div id="app">
    @yield('content')
</div>

<div class="dropzone-drag-visual">
    <div class="border">
        <div class="text">
            <p class="lead">Release me darlin', let me go...</p>
        </div>
    </div>
</div>
<!-- Scripts -->
<script src="{{ mix('/js/app.js') }}"></script>
<!-- Styles -->
<link href="{{ mix('/css/app.css') }}" rel="stylesheet">
</body>
</html>
