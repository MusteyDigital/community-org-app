<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink">Events</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-sand-200 p-4 sm:p-6">
                @if(session('success'))
                    <div class="mb-4 p-3 bg-teal-50 border border-teal-200 text-teal-800 rounded-lg text-sm">{{ session('success') }}</div>
                @endif
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
                    <a href="{{ route('events.create') }}" class="px-4 py-2 bg-gold-600 hover:bg-gold-700 text-white text-sm font-medium rounded-lg text-center">+ Add Event</a>
                    <form method="GET" action="{{ route('events.index') }}" class="flex flex-wrap items-end gap-2">
                        <div class="flex-1 min-w-[140px]">
                            <label class="block text-xs text-sand-500 mb-1">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Title or location" class="w-full border-sand-200 rounded-lg text-sm focus:border-teal-700 focus:ring-teal-700">
                        </div>
                        <div class="flex-1 min-w-[120px]">
                            <label class="block text-xs text-sand-500 mb-1">From</label>
                            <input type="date" name="from" value="{{ request('from') }}" class="w-full border-sand-200 rounded-lg text-sm focus:border-teal-700 focus:ring-teal-700">
                        </div>
                        <div class="flex-1 min-w-[120px]">
                            <label class="block text-xs text-sand-500 mb-1">To</label>
                            <input type="date" name="to" value="{{ request('to') }}" class="w-full border-sand-200 rounded-lg text-sm focus:border-teal-700 focus:ring-teal-700">
                        </div>
                        <button type="submit" class="px-3 py-2 bg-teal-800 hover:bg-teal-900 text-white text-sm font-medium rounded-lg">Filter</button>
                        @if(request()->anyFilled(['search', 'from', 'to']))
                            <a href="{{ route('events.index') }}" class="px-3 py-2 text-teal-700 text-sm font-medium">Clear</a>
                        @endif
                    </form>
                </div>
                <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-sand-200">
                            <th class="py-2 pr-4 font-mono text-xs uppercase tracking-widest text-teal-700 whitespace-nowrap">Title</th>
                            <th class="py-2 pr-4 font-mono text-xs uppercase tracking-widest text-teal-700 whitespace-nowrap">Date</th>
                            <th class="py-2 pr-4 font-mono text-xs uppercase tracking-widest text-teal-700 whitespace-nowrap">Time</th>
                            <th class="py-2 pr-4 font-mono text-xs uppercase tracking-widest text-teal-700 whitespace-nowrap">Location</th>
                            <th class="py-2 pr-4 font-mono text-xs uppercase tracking-widest text-teal-700 whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                        <tr class="border-b border-sand-100">
                            <td class="py-3 pr-4 text-ink font-medium whitespace-nowrap">{{ $event->title }}</td>
                            <td class="py-3 pr-4 font-mono text-sm text-clay-600 whitespace-nowrap">{{ $event->event_date }}</td>
                            <td class="py-3 pr-4 font-mono text-sm text-ink whitespace-nowrap">{{ $event->event_time }}</td>
                            <td class="py-3 pr-4 text-teal-800 whitespace-nowrap">{{ $event->location }}</td>
                            <td class="py-3 whitespace-nowrap">
                                <a href="{{ route('events.edit', $event) }}" class="text-teal-700 hover:text-teal-900 font-medium">Edit</a>
                                <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline" onsubmit="return confirm('Delete this event?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-clay-600 hover:text-clay-700 font-medium ml-3">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-6 text-sand-500">No events match your filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
                <div class="mt-4">{{ $events->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
