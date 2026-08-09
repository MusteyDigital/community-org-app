<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Community Org') }} &mdash; Manage your community, online</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,600,700|inter:400,500,600|ibm-plex-mono:400,500&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-sand-50 text-ink font-sans antialiased">
    <header class="bg-teal-900 text-sand-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-5 flex items-center justify-between">
            <a href="{{ url('/') }}" class="font-display font-bold text-lg">{{ config('app.name', 'Community Org') }}</a>
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ url('/browse') }}" class="text-sand-200 hover:text-white transition">Browse Organizations</a>
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-4 py-1.5 rounded-md bg-gold-500 text-teal-900 font-medium hover:bg-gold-600 transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sand-200 hover:text-white transition">Log in</a>
                    <a href="{{ route('register') }}" class="px-4 py-1.5 rounded-md bg-gold-500 text-teal-900 font-medium hover:bg-gold-600 transition">Register</a>
                @endauth
            </nav>
        </div>
    </header>
    <section class="relative overflow-hidden bg-teal-900 text-sand-50" style="background-image: repeating-linear-gradient(45deg, rgba(201,151,63,0.08) 0px, rgba(201,151,63,0.08) 2px, transparent 2px, transparent 12px);">
        <div class="max-w-4xl mx-auto px-6 lg:px-8 py-24 text-center">
            <p class="font-mono text-sm text-gold-500 uppercase tracking-widest mb-3">For churches, mosques &amp; community groups</p>
            <h1 class="font-display text-4xl sm:text-5xl font-bold">Run your community, all in one place</h1>
            <p class="text-sand-200 mt-5 max-w-xl mx-auto text-lg">Manage members, share events, and post announcements &mdash; with a public page your community can check anytime, no login required.</p>
            <div class="mt-8 flex items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="px-6 py-3 rounded-md bg-gold-500 text-teal-900 font-medium hover:bg-gold-600 transition">Register your organization</a>
                <a href="{{ url('/browse') }}" class="px-6 py-3 rounded-md border border-sand-400 text-sand-100 font-medium hover:bg-teal-800 transition">Browse organizations</a>
            </div>
        </div>
    </section>
    <main class="max-w-7xl mx-auto px-6 lg:px-8 py-16 space-y-14">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
            <div class="bg-white rounded-xl border border-sand-200 p-6">
                <p class="font-display text-3xl font-bold text-teal-900">{{ $organizationCount }}</p>
                <p class="text-sm text-teal-700 mt-1">Organizations registered</p>
            </div>
            <div class="bg-white rounded-xl border border-sand-200 p-6">
                <p class="font-display font-semibold text-lg text-ink">Members</p>
                <p class="text-sm text-sand-500 mt-1">Track who belongs, roles, and join dates</p>
            </div>
            <div class="bg-white rounded-xl border border-sand-200 p-6">
                <p class="font-display font-semibold text-lg text-ink">Events &amp; Announcements</p>
                <p class="text-sm text-sand-500 mt-1">Keep everyone in the loop, publicly</p>
            </div>
        </div>
        @if($organizations->count())
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display font-semibold text-xl text-ink">Featured Organizations</h2>
                <a href="{{ url('/browse') }}" class="text-sm text-gold-600 hover:text-gold-700">View all &rarr;</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($organizations as $org)
                    <a href="{{ url('/org/'.$org->slug) }}" class="block bg-white rounded-xl border border-sand-200 p-5 hover:border-gold-500 transition">
                        <h3 class="font-display font-semibold text-ink">{{ $org->name }}</h3>
                        <p class="text-sm text-teal-700 mt-1">{{ ucfirst($org->type) }} &middot; {{ $org->address }}</p>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </main>
    <footer class="border-t border-sand-200 py-6">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center text-sm text-sand-500">&copy; {{ date('Y') }} {{ config('app.name', 'Community Org') }}. All rights reserved.</div>
    </footer>
</body>
</html>
