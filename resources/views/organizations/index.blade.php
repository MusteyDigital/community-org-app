<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink">Organizations</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center justify-between">
                <p class="text-teal-700">Browse and join a church, mosque, or community.</p>
                <a href="{{ route('organizations.create') }}" class="px-4 py-2 rounded-md bg-gold-500 text-teal-900 font-medium hover:bg-gold-600 transition text-sm">
                    Register New Organization
                </a>
            </div>

            @if(session('status'))
                <div class="bg-teal-50 border border-teal-200 text-teal-800 rounded-lg px-4 py-3 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($organizations as $organization)
                    <div class="bg-white rounded-xl border border-sand-200 p-6">
                        <span class="text-xs font-mono uppercase tracking-widest text-gold-600 bg-gold-500/10 rounded px-2 py-0.5">
                            {{ ucfirst($organization->type) }}
                        </span>
                        <h3 class="font-display font-semibold text-lg text-ink mt-2">{{ $organization->name }}</h3>
                        @if($organization->address)
                            <p class="text-sm text-teal-700 mt-1">{{ $organization->address }}</p>
                        @endif
                        @if($organization->description)
                            <p class="text-sm text-sand-500 mt-2 line-clamp-2">{{ $organization->description }}</p>
                        @endif
                        <p class="text-xs text-sand-500 mt-3">{{ $organization->members_count }} member(s)</p>

                        <form method="POST" action="{{ route('organizations.join', $organization) }}" class="mt-4">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 rounded-md bg-teal-900 text-sand-50 font-medium hover:bg-teal-800 transition text-sm">
                                Request to Join
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-sand-500 text-sm">No organizations yet. Be the first to register one.</p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
