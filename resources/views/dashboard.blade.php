<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink">Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="relative overflow-hidden rounded-xl bg-teal-900 text-sand-50 p-8" style="background-image: repeating-linear-gradient(45deg, rgba(201,151,63,0.08) 0px, rgba(201,151,63,0.08) 2px, transparent 2px, transparent 12px);">
                <p class="font-mono text-sm text-gold-500 uppercase tracking-widest mb-1">Welcome back</p>
                <h1 class="font-display text-3xl font-bold">{{ Auth::user()->name }}</h1>
                <p class="text-sand-200 mt-2 max-w-xl">
                    @if($noOrganization)
                        You haven't joined an organization yet.
                    @else
                        Here's what's happening in your community right now.
                    @endif
                </p>
            </div>

            @if($noOrganization)
                <div class="bg-white rounded-xl border border-sand-200 p-10 text-center">
                    <h3 class="font-display font-semibold text-xl text-ink mb-2">Join a community to get started</h3>
                    <p class="text-sand-500 mb-6 max-w-md mx-auto">Browse churches, mosques, and communities and request to join one to see events, announcements, and members.</p>
                    <a href="{{ route('organizations.index') }}" class="inline-block px-6 py-2.5 rounded-md bg-gold-500 text-teal-900 font-medium hover:bg-gold-600 transition">
                        Browse Organizations
                    </a>
                </div>
            @else

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white rounded-xl border border-sand-200 p-6">
                        <p class="font-mono text-xs uppercase tracking-widest text-teal-700">Members</p>
                        <p class="font-display text-4xl font-bold text-ink mt-2">{{ $memberCount }}</p>
                        <a href="{{ route('members.index') }}" class="text-sm text-gold-600 hover:text-gold-700 mt-3 inline-block">View all &rarr;</a>
                    </div>
                    <div class="bg-white rounded-xl border border-sand-200 p-6">
                        <p class="font-mono text-xs uppercase tracking-widest text-teal-700">Events</p>
                        <p class="font-display text-4xl font-bold text-ink mt-2">{{ $eventCount }}</p>
                        <a href="{{ route('events.index') }}" class="text-sm text-gold-600 hover:text-gold-700 mt-3 inline-block">View all &rarr;</a>
                    </div>
                    <div class="bg-white rounded-xl border border-sand-200 p-6">
                        <p class="font-mono text-xs uppercase tracking-widest text-teal-700">Announcements</p>
                        <p class="font-display text-4xl font-bold text-ink mt-2">{{ $announcementCount }}</p>
                        <a href="{{ route('announcements.index') }}" class="text-sm text-gold-600 hover:text-gold-700 mt-3 inline-block">View all &rarr;</a>
                    </div>
                </div>

                @if($isAdmin)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl border border-sand-200 p-6">
                        <p class="font-mono text-xs uppercase tracking-widest text-teal-700">Pending Members</p>
                        <p class="font-display text-4xl font-bold text-ink mt-2">{{ $pendingMemberCount }}</p>
                        @if($pendingMemberCount > 0)
                            <a href="{{ route('members.index', ['status' => 'pending']) }}" class="text-sm text-gold-600 hover:text-gold-700 mt-3 inline-block">Review now &rarr;</a>
                        @else
                            <p class="text-sm text-sand-500 mt-3">All caught up</p>
                        @endif
                    </div>
                    <div class="bg-white rounded-xl border border-sand-200 p-6">
                        <p class="font-mono text-xs uppercase tracking-widest text-teal-700">Contributions This Month</p>
                        <p class="font-display text-4xl font-bold text-ink mt-2">NGN {{ number_format($contributionsThisMonth, 2) }}</p>
                        <p class="text-sm text-sand-500 mt-3">NGN {{ number_format($contributionsThisYear, 2) }} this year</p>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl border border-sand-200 p-6">
                        <h3 class="font-display font-semibold text-ink mb-4">Upcoming Events</h3>
                        @forelse($upcomingEvents as $event)
                            <div class="flex items-start gap-3 py-2 border-b border-sand-100 last:border-0">
                                <div class="font-mono text-xs text-clay-600 bg-sand-100 rounded px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($event->event_date)->format('M j') }}</div>
                                <div>
                                    <p class="font-medium text-ink">{{ $event->title }}</p>
                                    <p class="text-sm text-teal-700">{{ $event->location }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sand-500 text-sm">No upcoming events. <a href="{{ route('events.create') }}" class="text-gold-600">Add one &rarr;</a></p>
                        @endforelse
                    </div>
                    <div class="bg-white rounded-xl border border-sand-200 p-6">
                        <h3 class="font-display font-semibold text-ink mb-4">Pinned Announcements</h3>
                        @forelse($pinnedAnnouncements as $announcement)
                            <div class="py-2 border-b border-sand-100 last:border-0">
                                <p class="font-medium text-ink">{{ $announcement->title }}</p>
                                <p class="text-sm text-teal-700 line-clamp-1">{{ $announcement->body }}</p>
                            </div>
                        @empty
                            <p class="text-sand-500 text-sm">No pinned announcements. <a href="{{ route('announcements.create') }}" class="text-gold-600">Add one &rarr;</a></p>
                        @endforelse
                    </div>
                </div>

            @endif

        </div>
    </div>
</x-app-layout>


