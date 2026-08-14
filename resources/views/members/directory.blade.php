<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink">Member Directory</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-sand-200 p-4 sm:p-6">
                @if(session('status') === 'directory-visibility-updated')
                    <div class="mb-4 p-3 bg-teal-50 border border-teal-200 text-teal-800 rounded-lg text-sm">Your directory visibility preference was updated.</div>
                @endif
                <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-sand-200">
                            <th class="py-2 pr-4 font-mono text-xs uppercase tracking-widest text-teal-700 whitespace-nowrap">Name</th>
                            <th class="py-2 pr-4 font-mono text-xs uppercase tracking-widest text-teal-700 whitespace-nowrap">Role</th>
                            <th class="py-2 pr-4 font-mono text-xs uppercase tracking-widest text-teal-700 whitespace-nowrap">Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $member)
                        <tr class="border-b border-sand-100">
                            <td class="py-3 pr-4 text-ink font-medium whitespace-nowrap">{{ $member->name }}</td>
                            <td class="py-3 pr-4 whitespace-nowrap"><span class="text-xs px-2 py-1 rounded-full {{ $member->role == 'admin' ? 'bg-gold-500/20 text-gold-700' : 'bg-teal-800/10 text-teal-800' }}">{{ $member->role }}</span></td>
                            <td class="py-3 pr-4 font-mono text-sm text-ink whitespace-nowrap">{{ $member->join_date }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="py-6 text-sand-500">No members are listed in the directory right now.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
                <div class="mt-4">{{ $members->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>

