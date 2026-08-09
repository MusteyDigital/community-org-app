<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink">Members</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-sand-200 p-6">
                @if(session('success'))
                    <div class="mb-4 p-3 bg-teal-50 border border-teal-200 text-teal-800 rounded-lg text-sm">{{ session('success') }}</div>
                @endif
                <a href="{{ route('members.create') }}" class="inline-block mb-6 px-4 py-2 bg-gold-600 hover:bg-gold-700 text-white text-sm font-medium rounded-lg">+ Add Member</a>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-sand-200">
                            <th class="py-2 font-mono text-xs uppercase tracking-widest text-teal-700">Name</th>
                            <th class="py-2 font-mono text-xs uppercase tracking-widest text-teal-700">Email</th>
                            <th class="py-2 font-mono text-xs uppercase tracking-widest text-teal-700">Phone</th>
                            <th class="py-2 font-mono text-xs uppercase tracking-widest text-teal-700">Role</th>
                            <th class="py-2 font-mono text-xs uppercase tracking-widest text-teal-700">Status</th>
                            <th class="py-2 font-mono text-xs uppercase tracking-widest text-teal-700">Joined</th>
                            <th class="py-2 font-mono text-xs uppercase tracking-widest text-teal-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $member)
                        <tr class="border-b border-sand-100">
                            <td class="py-3 text-ink font-medium">{{ $member->name }}</td>
                            <td class="py-3 text-teal-800">{{ $member->email }}</td>
                            <td class="py-3 font-mono text-sm text-ink">{{ $member->phone }}</td>
                            <td class="py-3"><span class="text-xs px-2 py-1 rounded-full {{ $member->role == 'admin' ? 'bg-gold-500/20 text-gold-700' : 'bg-teal-800/10 text-teal-800' }}">{{ $member->role }}</span></td>
                            <td class="py-3">
                                @if($member->status === 'approved')
                                    <span class="text-xs px-2 py-1 rounded-full bg-teal-800/10 text-teal-800">Approved</span>
                                @elseif($member->status === 'pending')
                                    <span class="text-xs px-2 py-1 rounded-full bg-gold-500/20 text-gold-700">Pending</span>
                                @else
                                    <span class="text-xs px-2 py-1 rounded-full bg-sand-200 text-sand-500">Rejected</span>
                                @endif
                            </td>
                            <td class="py-3 font-mono text-sm text-ink">{{ $member->join_date }}</td>
                            <td class="py-3 whitespace-nowrap">
                                @if($member->status === 'pending')
                                    <form action="{{ route('members.approve', $member) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-teal-700 hover:text-teal-900 font-medium">Approve</button>
                                    </form>
                                    <form action="{{ route('members.reject', $member) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-clay-600 hover:text-clay-700 font-medium ml-3">Reject</button>
                                    </form>
                                @else
                                    <a href="{{ route('members.edit', $member) }}" class="text-teal-700 hover:text-teal-900 font-medium">Edit</a>
                                    <form action="{{ route('members.destroy', $member) }}" method="POST" class="inline" onsubmit="return confirm('Delete this member?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-clay-600 hover:text-clay-700 font-medium ml-3">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="py-6 text-sand-500">No members yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $members->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
