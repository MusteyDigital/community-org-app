<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $organization->name }} &mdash; {{ config('app.name', 'Community Org') }}</title>
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
    <section class="relative overflow-hidden bg-teal-900 text-sand-50" style="background-image: repeating-linear-gradient(45deg, rgba(201,151,63,0.08) 0px, rgba(201,151,63,0.08) 2px, transparent 2px, transparent 12px);">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16 text-center">
            <p class="font-mono text-sm text-gold-500 uppercase tracking-widest mb-3">{{ ucfirst($organization->type) }}</p>
            <h1 class="font-display text-4xl sm:text-5xl font-bold max-w-2xl mx-auto">{{ $organization->name }}</h1>
            @if($organization->address)
                <p class="text-sand-200 mt-4">{{ $organization->address }}</p>
            @endif
        </div>
    </section>
    <main class="max-w-7xl mx-auto px-6 lg:px-8 py-12 space-y-10">
        @if($organization->description)
            <div class="bg-white rounded-xl border border-sand-200 p-6">
                <p class="text-teal-800">{{ $organization->description }}</p>
            </div>
        @endif
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-sand-200 p-6">
                <h2 class="font-display font-semibold text-xl text-ink mb-4">Upcoming Events</h2>
                @forelse($upcomingEvents as $event)
                    <div class="flex items-start gap-3 py-3 border-b border-sand-100 last:border-0">
                        <div class="font-mono text-xs text-clay-600 bg-sand-100 rounded px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($event->event_date)->format('M j') }}</div>
                        <div>
                            <p class="font-medium text-ink">{{ $event->title }}</p>
                            @if($event->location)
                                <p class="text-sm text-teal-700">{{ $event->location }}</p>
                            @endif
                            @if($event->description)
                                <p class="text-sm text-sand-500 mt-1 line-clamp-2">{{ $event->description }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sand-500 text-sm">No upcoming events right now.</p>
                @endforelse
            </div>
            <div class="bg-white rounded-xl border border-sand-200 p-6">
                <h2 class="font-display font-semibold text-xl text-ink mb-4">Announcements</h2>
                @forelse($pinnedAnnouncements as $announcement)
                    <div class="py-3 border-b border-sand-100 last:border-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono uppercase tracking-widest text-gold-600 bg-gold-500/10 rounded px-2 py-0.5">Pinned</span>
                            <p class="font-medium text-ink">{{ $announcement->title }}</p>
                        </div>
                        <p class="text-sm text-teal-700 mt-1 line-clamp-2">{{ $announcement->body }}</p>
                    </div>
                @empty
                    <p class="text-sand-500 text-sm">No announcements pinned right now.</p>
                @endforelse
            </div>
        </div>
        @guest
            <div class="text-center bg-teal-900 text-sand-50 rounded-xl p-10">
                <h3 class="font-display text-2xl font-bold mb-2">Want to get involved?</h3>
                <p class="text-sand-200 mb-5">Create an account to join {{ $organization->name }} and stay in the loop.</p>
                <a href="{{ route('register') }}" class="inline-block px-6 py-2.5 rounded-md bg-gold-500 text-teal-900 font-medium hover:bg-gold-600 transition">Get Started</a>
            </div>
        @endguest
    </main>
    <footer class="border-t border-sand-200 py-6">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center text-sm text-sand-500">&copy; {{ date('Y') }} {{ config('app.name', 'Community Org') }}. All rights reserved.</div>
    </footer>
</body>
</html>
