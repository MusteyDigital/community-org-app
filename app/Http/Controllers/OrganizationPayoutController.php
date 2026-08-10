<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrganizationPayoutController extends Controller
{
    private function currentMembership()
    {
        $membership = Member::where('user_id', Auth::id())->where('status', 'approved')->first();
        abort_unless($membership, 403, 'You must be an approved member of an organization to view this.');
        return $membership;
    }

    private function assertIsOrgAdmin()
    {
        $membership = $this->currentMembership();
        abort_unless($membership->role === 'admin', 403, 'Only organization admins can manage payout settings.');
    }

    public function edit()
    {
        $this->assertIsOrgAdmin();
        $organization = $this->currentMembership()->organization;

        $banks = collect();
        $response = Http::withToken(config('services.paystack.secret'))
            ->get('https://api.paystack.co/bank', ['country' => 'nigeria']);

        if ($response->successful() && $response->json('status')) {
            $banks = collect($response->json('data'));
        }

        return view('organizations.payout', compact('organization', 'banks'));
    }

    public function resolve(Request $request)
    {
        $this->assertIsOrgAdmin();

        $validated = $request->validate([
            'account_number' => 'required|digits:10',
            'bank_code' => 'required|string',
        ]);

        $response = Http::withToken(config('services.paystack.secret'))
            ->get('https://api.paystack.co/bank/resolve', [
                'account_number' => $validated['account_number'],
                'bank_code' => $validated['bank_code'],
            ]);

        if (! $response->successful() || ! $response->json('status')) {
            return response()->json(['error' => $response->json('message', 'Could not verify account.')], 422);
        }

        return response()->json(['account_name' => $response->json('data.account_name')]);
    }

    public function update(Request $request)
    {
        $this->assertIsOrgAdmin();
        $organization = $this->currentMembership()->organization;

        $validated = $request->validate([
            'bank_code' => 'required|string',
            'bank_name' => 'required|string',
            'account_number' => 'required|digits:10',
            'account_name' => 'required|string',
        ]);

        $response = Http::withToken(config('services.paystack.secret'))
            ->post('https://api.paystack.co/subaccount', [
                'business_name' => $organization->name,
                'settlement_bank' => $validated['bank_code'],
                'account_number' => $validated['account_number'],
                'percentage_charge' => 2,
            ]);

        if (! $response->successful() || ! $response->json('status')) {
            Log::warning('Paystack subaccount creation failed: '.$response->body());
            return back()->with('error', 'Could not set up payouts. Please check your details and try again.');
        }

        $validated['paystack_subaccount_code'] = $response->json('data.subaccount_code');
        $organization->update($validated);

        return redirect()->route('organizations.payout.edit')->with('success', 'Payout account connected successfully.');
    }
}
