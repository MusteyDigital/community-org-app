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
                <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
                    <a href="{{ route('announcements.create') }}" class="px-4 py-2 bg-gold-600 hover:bg-gold-700 text-white text-sm font-medium rounded-lg">+ Add Announcement</a>
                    <form method="GET" action="{{ route('announcements.index') }}" class="flex flex-wrap items-end gap-2">
                        <div>
                            <label class="block text-xs text-sand-500 mb-1">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Title or body" class="border-sand-200 rounded-lg text-sm focus:border-teal-700 focus:ring-teal-700">
                        </div>
                        <div>
                            <label class="block text-xs text-sand-500 mb-1">Type</label>
                            <select name="type" class="border-sand-200 rounded-lg text-sm focus:border-teal-700 focus:ring-teal-700">
                                <option value="">All</option>
                                <option value="general" {{ request('type') == 'general' ? 'selected' : '' }}>General</option>
                                <option value="burial" {{ request('type') == 'burial' ? 'selected' : '' }}>Burial Notice</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-sand-500 mb-1">Pinned</label>
                            <select name="pinned" class="border-sand-200 rounded-lg text-sm focus:border-teal-700 focus:ring-teal-700">
                                <option value="">All</option>
                                <option value="1" {{ request('pinned') === '1' ? 'selected' : '' }}>Pinned only</option>
                                <option value="0" {{ request('pinned') === '0' ? 'selected' : '' }}>Not pinned</option>
                            </select>
                        </div>
                        <button type="submit" class="px-3 py-2 bg-teal-800 hover:bg-teal-900 text-white text-sm font-medium rounded-lg">Filter</button>
                        @if(request()->anyFilled(['search', 'type', 'pinned']))
                            <a href="{{ route('announcements.index') }}" class="px-3 py-2 text-teal-700 text-sm font-medium">Clear</a>
                        @endif
                    </form>
                </div>
                @forelse($announcements as $announcement)
                <div class="border-b border-sand-100 py-4 last:border-0">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-display font-semibold text-ink">
                                {{ $announcement->title }}
                                @if($announcement->is_pinned)<span class="text-xs bg-gold-500/20 text-gold-700 px-2 py-1 rounded-full ml-2">Pinned</span>@endif
                                <span class="text-xs {{ $announcement->type === 'burial' ? 'bg-clay-600/10 text-clay-700' : 'bg-teal-800/10 text-teal-800' }} px-2 py-1 rounded-full ml-1">{{ $announcement->type === 'burial' ? 'Burial Notice' : 'General' }}</span>
                            </h3>
                            <p class="text-teal-800 mt-1 whitespace-pre-line">{{ $announcement->body }}</p>
                            <p class="font-mono text-xs text-sand-500 mt-2">{{ $announcement->published_at }}</p>
                        </div>
                        @if(optional(Auth::user()->approvedMembership)->role === 'admin')
<div class="flex-shrink-0 ml-4">
                            <a href="{{ route('announcements.edit', $announcement) }}" class="text-teal-700 hover:text-teal-900 font-medium">Edit</a>
                            <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="inline" onsubmit="return confirm('Delete this announcement?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-clay-600 hover:text-clay-700 font-medium ml-3">Delete</button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-sand-500 py-6">No announcements match your filters.</p>
                @endforelse
                <div class="mt-4">{{ $announcements->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>



