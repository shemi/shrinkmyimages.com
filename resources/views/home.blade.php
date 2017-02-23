@extends('layouts.app')

@section('content')

    <div class="page" id="page">

        <header class="app-header">
            <router-link to="/" class="brand">
                <img src="{{ url('/images/shrink-logo.png') }}" alt="shrink my images logo">
            </router-link>

            @include('blade.navigation')
        </header>

        <main class="main-content">
            <router-view></router-view>
            <upload-card></upload-card>
        </main>

        <ad-manager></ad-manager>

    </div>

@endsection
