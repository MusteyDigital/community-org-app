<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink">Contributions</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-sand-200 p-4 sm:p-6">
                @if(session('success'))
                    <div class="mb-4 p-3 bg-teal-50 border border-teal-200 text-teal-800 rounded-lg text-sm">{{ session('success') }}</div>
                @endif

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <p class="font-mono text-xs uppercase tracking-widest text-teal-700">{{ $isAdmin ? 'Total Contributions (Organization)' : 'Your Total Contributions' }}</p>
                        <p class="font-display text-3xl font-bold text-ink mt-1">&#8358;{{ number_format($total, 2) }}</p>
                    </div>
                    @if($isAdmin)
                        <a href="{{ route('contributions.create') }}" class="px-4 py-2 bg-gold-600 hover:bg-gold-700 text-white text-sm font-medium rounded-lg text-center">+ Record Contribution</a>
                    @else
                        <a href="{{ route('paystack.pay') }}" class="px-4 py-2 bg-gold-600 hover:bg-gold-700 text-white text-sm font-medium rounded-lg text-center">Make a Contribution</a>
                    @endif
                </div>

                <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-sand-200">
                            @if($isAdmin)
                                <th class="py-2 pr-4 font-mono text-xs uppercase tracking-widest text-teal-700 whitespace-nowrap">Member</th>
                            @endif
                            <th class="py-2 pr-4 font-mono text-xs uppercase tracking-widest text-teal-700 whitespace-nowrap">Amount</th>
                            <th class="py-2 pr-4 font-mono text-xs uppercase tracking-widest text-teal-700 whitespace-nowrap">Category</th>
                            <th class="py-2 pr-4 font-mono text-xs uppercase tracking-widest text-teal-700 whitespace-nowrap">Date</th>
                            <th class="py-2 pr-4 font-mono text-xs uppercase tracking-widest text-teal-700 whitespace-nowrap">Receipt</th>
                            @if($isAdmin)
                                <th class="py-2 pr-4 font-mono text-xs uppercase tracking-widest text-teal-700 whitespace-nowrap">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contributions as $contribution)
                        <tr class="border-b border-sand-100">
                            @if($isAdmin)
                                <td class="py-3 pr-4 text-ink font-medium whitespace-nowrap">{{ $contribution->member->name ?? 'Ã¢â‚¬â€' }}</td>
                            @endif
                            <td class="py-3 pr-4 font-mono text-sm text-ink whitespace-nowrap">&#8358;{{ number_format($contribution->amount, 2) }}</td>
                            <td class="py-3 pr-4 whitespace-nowrap"><span class="text-xs px-2 py-1 rounded-full bg-teal-800/10 text-teal-800">{{ ucfirst($contribution->category) }}</span></td>
                            <td class="py-3 pr-4 font-mono text-sm text-ink whitespace-nowrap">{{ $contribution->contributed_at->format('M j, Y') }}</td>
                            <td class="py-3 pr-4 whitespace-nowrap">
                                <a href="{{ route('contributions.receipt', $contribution) }}" class="text-teal-700 hover:text-teal-900 font-medium">Download</a>
                            </td>
                            @if($isAdmin)
                                <td class="py-3 whitespace-nowrap">
                                    <a href="{{ route('contributions.edit', $contribution) }}" class="text-teal-700 hover:text-teal-900 font-medium">Edit</a>
                                    <form action="{{ route('contributions.destroy', $contribution) }}" method="POST" class="inline" onsubmit="return confirm('Delete this contribution?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-clay-600 hover:text-clay-700 font-medium ml-3">Delete</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr><td colspan="{{ $isAdmin ? 6 : 4 }}" class="py-6 text-sand-500">No contributions recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
                <div class="mt-4">{{ $contributions->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>




