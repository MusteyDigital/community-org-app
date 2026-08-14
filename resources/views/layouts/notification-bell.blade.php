<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <button @click="open = ! open" class="relative inline-flex items-center p-2 rounded-md text-sand-100 hover:bg-teal-800 focus:outline-none transition ease-in-out duration-150">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-clay-600 rounded-full">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
        @endif
    </button>
    <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute z-50 mt-2 w-80 rounded-md shadow-lg end-0 origin-top-right"
            style="display: none;">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 bg-white max-h-96 overflow-y-auto">
            <div class="flex items-center justify-between px-4 py-2 border-b border-sand-100">
                <span class="text-sm font-semibold text-ink">Notifications</span>
                @if($unreadCount > 0)
                    <form method="POST" action="{{ route('notifications.readAll') }}">
                        @csrf
                        <button type="submit" class="text-xs text-teal-700 hover:text-teal-900 font-medium">Mark all read</button>
                    </form>
                @endif
            </div>
            @forelse($notifications as $notification)
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-3 border-b border-sand-100 last:border-0 hover:bg-sand-50 {{ $notification->read_at ? '' : 'bg-teal-50' }}">
                        <p class="text-sm text-ink font-medium">{{ $notification->data['title'] ?? 'Notification' }}</p>
                        @if(!empty($notification->data['organization']))
                            <p class="text-xs text-sand-500 mt-0.5">{{ $notification->data['organization'] }}</p>
                        @endif
                        <p class="text-xs text-sand-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </button>
                </form>
            @empty
                <p class="px-4 py-6 text-sm text-sand-500 text-center">No notifications yet.</p>
            @endforelse
        </div>
    </div>
</div>