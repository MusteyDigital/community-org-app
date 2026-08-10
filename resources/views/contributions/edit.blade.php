<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink">Edit Contribution</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-sand-200 p-6">
                <form method="POST" action="{{ route('contributions.update', $contribution) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-ink mb-1">Member</label>
                        <select name="member_id" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700" required>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}" {{ old('member_id', $contribution->member_id) == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-ink mb-1">Amount (₦)</label>
                        <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount', $contribution->amount) }}" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700" required>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-ink mb-1">Category</label>
                        <select name="category" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700" required>
                            <option value="general" {{ old('category', $contribution->category) === 'general' ? 'selected' : '' }}>General</option>
                            <option value="tithe" {{ old('category', $contribution->category) === 'tithe' ? 'selected' : '' }}>Tithe</option>
                            <option value="zakat" {{ old('category', $contribution->category) === 'zakat' ? 'selected' : '' }}>Zakat</option>
                            <option value="building fund" {{ old('category', $contribution->category) === 'building fund' ? 'selected' : '' }}>Building Fund</option>
                            <option value="charity" {{ old('category', $contribution->category) === 'charity' ? 'selected' : '' }}>Charity</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-ink mb-1">Date</label>
                        <input type="date" name="contributed_at" value="{{ old('contributed_at', $contribution->contributed_at->format('Y-m-d')) }}" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700" required>
                    </div>
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-ink mb-1">Note (optional)</label>
                        <textarea name="note" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700" rows="3">{{ old('note', $contribution->note) }}</textarea>
                    </div>
                    @error('member_id')<p class="text-clay-600 text-sm mb-2">{{ $message }}</p>@enderror
                    @error('amount')<p class="text-clay-600 text-sm mb-2">{{ $message }}</p>@enderror
                    @error('category')<p class="text-clay-600 text-sm mb-2">{{ $message }}</p>@enderror
                    @error('contributed_at')<p class="text-clay-600 text-sm mb-2">{{ $message }}</p>@enderror
                    <div class="flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-gold-600 hover:bg-gold-700 text-white text-sm font-medium rounded-lg">Update</button>
                        <a href="{{ route('contributions.index') }}" class="px-4 py-2 text-teal-800 text-sm font-medium">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

