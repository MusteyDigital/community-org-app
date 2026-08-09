<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink">Organization Approvals</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('status'))
                <div class="bg-teal-50 border border-teal-200 text-teal-800 rounded-lg px-4 py-3 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div>
                <h3 class="font-display font-semibold text-lg text-ink mb-4">Pending ({{ $pending->count() }})</h3>
                <div class="space-y-3">
                    @forelse($pending as $org)
                        <div class="bg-white rounded-xl border border-sand-200 p-5 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-mono uppercase tracking-widest text-gold-600">{{ ucfirst($org->type) }}</span>
                                <p class="font-medium text-ink">{{ $org->name }}</p>
                                <p class="text-sm text-sand-500">Submitted by {{ $org->creator->name ?? 'Unknown' }} &middot; {{ $org->address }}</p>
                            </div>
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('admin.organizations.approve', $org) }}">
                                    @csrf
                                    <button class="px-4 py-1.5 rounded-md bg-teal-900 text-sand-50 text-sm font-medium hover:bg-teal-800 transition">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.organizations.reject', $org) }}">
                                    @csrf
                                    <button class="px-4 py-1.5 rounded-md bg-white border border-sand-200 text-sand-500 text-sm font-medium hover:bg-sand-100 transition">Reject</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sand-500 text-sm">No pending organizations.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <h3 class="font-display font-semibold text-lg text-ink mb-4">Approved ({{ $approved->count() }})</h3>
                <div class="space-y-3">
                    @forelse($approved as $org)
                        <div class="bg-white rounded-xl border border-sand-200 p-5">
                            <span class="text-xs font-mono uppercase tracking-widest text-gold-600">{{ ucfirst($org->type) }}</span>
                            <p class="font-medium text-ink">{{ $org->name }}</p>
                        </div>
                    @empty
                        <p class="text-sand-500 text-sm">No approved organizations yet.</p>
                    @endforelse
                </div>
            </div>

            @if($rejected->count())
                <div>
                    <h3 class="font-display font-semibold text-lg text-ink mb-4">Rejected ({{ $rejected->count() }})</h3>
                    <div class="space-y-3">
                        @foreach($rejected as $org)
                            <div class="bg-white rounded-xl border border-sand-200 p-5">
                                <p class="font-medium text-ink">{{ $org->name }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
