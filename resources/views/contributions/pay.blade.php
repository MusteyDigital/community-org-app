<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink">Make a Contribution</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-sand-200 p-6">
                @if(session('error'))
                    <div class="mb-4 p-3 bg-clay-50 border border-clay-200 text-clay-700 rounded-lg text-sm">{{ session('error') }}</div>
                @endif

                <p class="text-sand-500 text-sm mb-6">You'll be redirected to Paystack to complete your payment securely.</p>

                <form method="POST" action="{{ route('paystack.initialize') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-ink mb-1">Amount (&#8358;)</label>
                        <input type="number" step="0.01" min="100" name="amount" value="{{ old('amount') }}" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700" required>
                        <p class="text-xs text-sand-500 mt-1">Minimum &#8358;100</p>
                    </div>
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-ink mb-1">Category</label>
                        <select name="category" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700" required>
                            <option value="general">General</option>
                            <option value="tithe">Tithe</option>
                            <option value="zakat">Zakat</option>
                            <option value="building fund">Building Fund</option>
                            <option value="charity">Charity</option>
                        </select>
                    </div>
                    @error('amount')<p class="text-clay-600 text-sm mb-2">{{ $message }}</p>@enderror
                    @error('category')<p class="text-clay-600 text-sm mb-2">{{ $message }}</p>@enderror
                    <div class="flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-gold-600 hover:bg-gold-700 text-white text-sm font-medium rounded-lg">Continue to Payment</button>
                        <a href="{{ route('contributions.index') }}" class="px-4 py-2 text-teal-800 text-sm font-medium">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
