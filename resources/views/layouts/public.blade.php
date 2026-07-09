<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <title>@yield('title', config('app.name'))</title>
        <meta name="description" content="@yield('meta_description', 'Gerai Dempo — Katering dimsum, gorengan, dan dessert lezat untuk berbagai acara di Jakarta Pusat.')">

        @fonts

        @vite('resources/css/app.css')
    </head>
    <body class="bg-brand-cream font-sans text-brand-brown antialiased">
        @include('layouts.partials.public-nav')

        <main>
            @yield('content')
        </main>

        @include('layouts.partials.public-footer')
        @include('layouts.partials.whatsapp-button')
    </body>
</html>
