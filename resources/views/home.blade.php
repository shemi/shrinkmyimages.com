@extends('layouts.app')

@section('content')

    <div class="page" id="page">

        <header class="app-header">
            <a href="/" class="brand">
                <img src="{{ url('/images/shrink-logo.png') }}" alt="shrink my images logo">
            </a>
        </header>

        <main class="main-content">
            <router-view></router-view>
        </main>

        <ad-manager></ad-manager>

    </div>

@endsection
