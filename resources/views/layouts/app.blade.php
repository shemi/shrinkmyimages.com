<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} | {{ config('app.slogan') }}</title>
    <meta name="application-name" content="{{ config('app.name') }} - {{ config('app.slogan') }}"/>
    <meta name="description" content="Compress and optimize your images for web use, We can compress your images up to 98% for less bandwidth and better user experience.">
    <!-- Styles -->
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">

    @include('layouts.icons')

    <base href="/">

    <!-- Scripts -->
    <script>
        window.SMI = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
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
</body>
</html>
