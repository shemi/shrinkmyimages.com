@extends('layouts.app')

@section('content')

    <div class="page" id="page">

        <header class="app-header">

        </header>

        <main class="main-content">
            <router-view></router-view>
        </main>

        <ad-manager></ad-manager>

    </div>

@endsection
