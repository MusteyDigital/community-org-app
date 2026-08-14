<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink">Trashed Announcements</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-sand-200 p-6">
                @if(session('success'))
                    <div class="mb-4 p-3 bg-teal-50 border border-teal-200 text-teal-800 rounded-lg text-sm">{{ session('success') }}</div>
                @endif
                <div class="flex items-center justify-between mb-6">
                    <a href="{{ route('announcements.index') }}" class="text-teal-700 hover:text-teal-900 text-sm font-medium">&larr; Back to Announcements</a>
                </div>
                @forelse($announcements as $announcement)
                <div class="border-b border-sand-100 py-4 last:border-0">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-display font-semibold text-ink">
                                {{ $announcement->title }}
                                <span class="text-xs {{ $announcement->type === 'burial' ? 'bg-clay-600/10 text-clay-700' : 'bg-teal-800/10 text-teal-800' }} px-2 py-1 rounded-full ml-1">{{ $announcement->type === 'burial' ? 'Burial Notice' : 'General' }}</span>
                            </h3>
                            <p class="text-teal-800 mt-1 whitespace-pre-line">{{ $announcement->body }}</p>
                            <p class="font-mono text-xs text-sand-500 mt-2">Deleted {{ $announcement->deleted_at }}</p>
                        </div>
                        <div class="flex-shrink-0 ml-4">
                            <form action="{{ route('announcements.restore', $announcement->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-teal-700 hover:text-teal-900 font-medium">Restore</button>
                            </form>
                            <form action="{{ route('announcements.forceDelete', $announcement->id) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete this announcement? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-clay-600 hover:text-clay-700 font-medium ml-3">Delete Forever</button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-sand-500 py-6">Trash is empty.</p>
                @endforelse
                <div class="mt-4">{{ $announcements->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
