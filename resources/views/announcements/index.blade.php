<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink">Announcements</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-sand-200 p-6">
                @if(session('success'))
                    <div class="mb-4 p-3 bg-teal-50 border border-teal-200 text-teal-800 rounded-lg text-sm">{{ session('success') }}</div>
                @endif
                <a href="{{ route('announcements.create') }}" class="inline-block mb-6 px-4 py-2 bg-gold-600 hover:bg-gold-700 text-white text-sm font-medium rounded-lg">+ Add Announcement</a>
                @forelse($announcements as $announcement)
                <div class="border-b border-sand-100 py-4 last:border-0">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-display font-semibold text-ink">{{ $announcement->title }} @if($announcement->is_pinned)<span class="text-xs bg-gold-500/20 text-gold-700 px-2 py-1 rounded-full ml-2">Pinned</span>@endif</h3>
                            <p class="text-teal-800 mt-1">{{ $announcement->body }}</p>
                            <p class="font-mono text-xs text-sand-500 mt-2">{{ $announcement->published_at }}</p>
                        </div>
                        <div class="flex-shrink-0 ml-4">
                            <a href="{{ route('announcements.edit', $announcement) }}" class="text-teal-700 hover:text-teal-900 font-medium">Edit</a>
                            <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="inline" onsubmit="return confirm('Delete this announcement?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-clay-600 hover:text-clay-700 font-medium ml-3">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-sand-500 py-6">No announcements yet.</p>
                @endforelse
                <div class="mt-4">{{ $announcements->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
