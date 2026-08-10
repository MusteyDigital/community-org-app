<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaystackController extends Controller
{
    private function currentMembership()
    {
        $membership = Member::where('user_id', Auth::id())->where('status', 'approved')->first();

        abort_unless($membership, 403, 'You must be an approved member of an organization to do this.');

        return $membership;
    }

    public function pay()
    {
        $this->currentMembership();

        return view('contributions.pay');
    }

    public function initialize(Request $request)
    {
        $membership = $this->currentMembership();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:100',
            'category' => 'required|string|max:100',
        ]);

        $reference = 'contrib_'.Str::uuid();

        $response = Http::withToken(config('services.paystack.secret'))
            ->post('https://api.paystack.co/transaction/initialize', [
                'email' => Auth::user()->email,
                'amount' => (int) round($validated['amount'] * 100),
                'reference' => $reference,
                'callback_url' => route('paystack.callback'),
                'metadata' => [
                    'member_id' => $membership->id,
                    'organization_id' => $membership->organization_id,
                    'category' => $validated['category'],
                ],
            ]);

        if (! $response->successful() || ! $response->json('status')) {
            Log::warning('Paystack initialize failed: '.$response->body());
            return back()->with('error', 'Could not start payment. Please try again.');
        }

        return redirect($response->json('data.authorization_url'));
    }

    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (! $reference) {
            return redirect()->route('contributions.index')->with('error', 'Missing payment reference.');
        }

        $response = Http::withToken(config('services.paystack.secret'))
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        if (! $response->successful() || ! $response->json('status')) {
            Log::warning('Paystack verify failed: '.$response->body());
            return redirect()->route('contributions.index')->with('error', 'Payment verification failed.');
        }

        $data = $response->json('data');

        if ($data['status'] !== 'success') {
            return redirect()->route('contributions.index')->with('error', 'Payment was not successful.');
        }

        $existing = Contribution::where('payment_reference', $reference)->first();
        if ($existing) {
            return redirect()->route('contributions.index')->with('success', 'Payment already recorded.');
        }

        $metadata = $data['metadata'] ?? [];

        Contribution::create([
            'organization_id' => $metadata['organization_id'] ?? null,
            'member_id' => $metadata['member_id'] ?? null,
            'amount' => $data['amount'] / 100,
            'category' => $metadata['category'] ?? 'general',
            'contributed_at' => now(),
            'payment_reference' => $reference,
            'source' => 'self',
            'payment_status' => 'completed',
        ]);

        return redirect()->route('contributions.index')->with('success', 'Thank you! Your contribution was recorded.');
    }
}
