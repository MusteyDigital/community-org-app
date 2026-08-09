<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Organizations &mdash; {{ config('app.name', 'Community Org') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,600,700|inter:400,500,600|ibm-plex-mono:400,500&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-sand-50 text-ink font-sans antialiased">
    <header class="bg-teal-900 text-sand-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-5 flex items-center justify-between">
            <a href="{{ url('/') }}" class="font-display font-bold text-lg">{{ config('app.name', 'Community Org') }}</a>
            <nav class="flex items-center gap-4 text-sm">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-4 py-1.5 rounded-md bg-gold-500 text-teal-900 font-medium hover:bg-gold-600 transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sand-200 hover:text-white transition">Log in</a>
                    <a href="{{ route('register') }}" class="px-4 py-1.5 rounded-md bg-gold-500 text-teal-900 font-medium hover:bg-gold-600 transition">Register</a>
                @endauth
            </nav>
        </div>
    </header>
    <main class="max-w-4xl mx-auto px-6 lg:px-8 py-12">
        <p class="font-mono text-sm text-gold-600 uppercase tracking-widest mb-1">Directory</p>
        <h1 class="font-display text-3xl font-bold text-ink mb-8">Community Organizations</h1>
        <div class="space-y-4">
            @forelse($organizations as $org)
                <a href="{{ url('/org/'.$org->slug) }}" class="block bg-white rounded-xl border border-sand-200 p-5 hover:border-gold-500 transition">
                    <h2 class="font-display font-semibold text-lg text-ink">{{ $org->name }}</h2>
                    <p class="text-sm text-teal-700 mt-1">{{ ucfirst($org->type) }} &middot; {{ $org->address }}</p>
                    @if($org->description)
                        <p class="text-sm text-sand-500 mt-2 line-clamp-2">{{ $org->description }}</p>
                    @endif
                </a>
            @empty
                <p class="text-sand-500">No organizations listed yet.</p>
            @endforelse
        </div>
    </main>
    <footer class="border-t border-sand-200 py-6 mt-10">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center text-sm text-sand-500">&copy; {{ date('Y') }} {{ config('app.name', 'Community Org') }}. All rights reserved.</div>
    </footer>
</body>
</html>
