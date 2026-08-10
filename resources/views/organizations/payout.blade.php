<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink">Payout Settings</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-sand-200 p-6">
                @if(session('success'))
                    <div class="mb-4 p-3 bg-teal-50 border border-teal-200 text-teal-800 rounded-lg text-sm">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-3 bg-clay-50 border border-clay-200 text-clay-700 rounded-lg text-sm">{{ session('error') }}</div>
                @endif

                @if($organization->hasPayoutSetup())
                    <div class="mb-6 p-4 bg-teal-50 border border-teal-200 rounded-lg">
                        <p class="font-medium text-teal-800">Payouts connected</p>
                        <p class="text-sm text-teal-700 mt-1">{{ $organization->account_name }} &mdash; {{ $organization->bank_name }} ({{ $organization->account_number }})</p>
                        <p class="text-xs text-sand-500 mt-2">Member contributions are now sent directly to this account. Update the details below to change it.</p>
                    </div>
                @else
                    <p class="text-sand-500 text-sm mb-6">Connect your bank account so member contributions are paid directly to your organization, not held elsewhere.</p>
                @endif

                <form method="POST" action="{{ route('organizations.payout.update') }}" id="payout-form">
                    @csrf
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-ink mb-1">Bank</label>
                        <select name="bank_code" id="bank_code" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700" required>
                            <option value="">Select your bank</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank['code'] }}" data-name="{{ $bank['name'] }}" {{ old('bank_code', $organization->bank_code) == $bank['code'] ? 'selected' : '' }}>{{ $bank['name'] }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="bank_name" id="bank_name" value="{{ $organization->bank_name }}">
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-ink mb-1">Account Number</label>
                        <input type="text" name="account_number" id="account_number" maxlength="10" value="{{ old('account_number', $organization->account_number) }}" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700" required>
                    </div>
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-ink mb-1">Account Name</label>
                        <input type="text" name="account_name" id="account_name" value="{{ old('account_name', $organization->account_name) }}" class="w-full border-sand-200 rounded-lg bg-sand-50" readonly placeholder="Will be verified automatically">
                        <p id="verify-status" class="text-xs mt-1"></p>
                    </div>
                    @error('bank_code')<p class="text-clay-600 text-sm mb-2">{{ $message }}</p>@enderror
                    @error('account_number')<p class="text-clay-600 text-sm mb-2">{{ $message }}</p>@enderror
                    <button type="submit" id="submit-btn" class="px-4 py-2 bg-gold-600 hover:bg-gold-700 text-white text-sm font-medium rounded-lg disabled:opacity-50" disabled>Save Payout Details</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const bankSelect = document.getElementById('bank_code');
        const acctNumber = document.getElementById('account_number');
        const acctName = document.getElementById('account_name');
        const bankName = document.getElementById('bank_name');
        const submitBtn = document.getElementById('submit-btn');
        const status = document.getElementById('verify-status');

        async function tryResolve() {
            if (bankSelect.value && acctNumber.value.length === 10) {
                status.textContent = 'Verifying...';
                status.className = 'text-xs mt-1 text-sand-500';
                submitBtn.disabled = true;

                const res = await fetch('{{ route("organizations.payout.resolve") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ bank_code: bankSelect.value, account_number: acctNumber.value }),
                });
                const data = await res.json();

                if (res.ok) {
                    acctName.value = data.account_name;
                    bankName.value = bankSelect.options[bankSelect.selectedIndex].dataset.name;
                    status.textContent = 'Verified: ' + data.account_name;
                    status.className = 'text-xs mt-1 text-teal-700';
                    submitBtn.disabled = false;
                } else {
                    acctName.value = '';
                    status.textContent = data.error || 'Could not verify account.';
                    status.className = 'text-xs mt-1 text-clay-600';
                    submitBtn.disabled = true;
                }
            }
        }

        bankSelect.addEventListener('change', tryResolve);
        acctNumber.addEventListener('input', tryResolve);
    </script>
</x-app-layout>
