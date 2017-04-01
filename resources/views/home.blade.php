@extends('layouts.app')

@section('content')

    <div class="page" id="page">

        <header class="app-header">
            @include('blade.navigation')
        </header>

        <main class="main-content">
            <router-view></router-view>
            <upload-card></upload-card>
        </main>

        <ad-manager></ad-manager>

    </div>

@endsection
