<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink">Events</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-sand-200 p-6">
                @if(session('success'))
                    <div class="mb-4 p-3 bg-teal-50 border border-teal-200 text-teal-800 rounded-lg text-sm">{{ session('success') }}</div>
                @endif
                <a href="{{ route('events.create') }}" class="inline-block mb-6 px-4 py-2 bg-gold-600 hover:bg-gold-700 text-white text-sm font-medium rounded-lg">+ Add Event</a>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-sand-200">
                            <th class="py-2 font-mono text-xs uppercase tracking-widest text-teal-700">Title</th>
                            <th class="py-2 font-mono text-xs uppercase tracking-widest text-teal-700">Date</th>
                            <th class="py-2 font-mono text-xs uppercase tracking-widest text-teal-700">Time</th>
                            <th class="py-2 font-mono text-xs uppercase tracking-widest text-teal-700">Location</th>
                            <th class="py-2 font-mono text-xs uppercase tracking-widest text-teal-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                        <tr class="border-b border-sand-100">
                            <td class="py-3 text-ink font-medium">{{ $event->title }}</td>
                            <td class="py-3 font-mono text-sm text-clay-600">{{ $event->event_date }}</td>
                            <td class="py-3 font-mono text-sm text-ink">{{ $event->event_time }}</td>
                            <td class="py-3 text-teal-800">{{ $event->location }}</td>
                            <td class="py-3">
                                <a href="{{ route('events.edit', $event) }}" class="text-teal-700 hover:text-teal-900 font-medium">Edit</a>
                                <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline" onsubmit="return confirm('Delete this event?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-clay-600 hover:text-clay-700 font-medium ml-3">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-6 text-sand-500">No events yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $events->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
