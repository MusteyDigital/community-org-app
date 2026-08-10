<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContributionController extends Controller
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

        abort_unless($membership->role === 'admin', 403, 'Only organization admins can manage contributions.');
    }

    public function index()
    {
        $membership = $this->currentMembership();
        $orgId = $membership->organization_id;

        if ($membership->role === 'admin') {
            $contributions = Contribution::where('organization_id', $orgId)
                ->with('member')
                ->latest('contributed_at')
                ->paginate(15);

            $total = Contribution::where('organization_id', $orgId)->sum('amount');
        } else {
            $contributions = Contribution::where('organization_id', $orgId)
                ->where('member_id', $membership->id)
                ->latest('contributed_at')
                ->paginate(15);

            $total = Contribution::where('organization_id', $orgId)
                ->where('member_id', $membership->id)
                ->sum('amount');
        }

        $isAdmin = $membership->role === 'admin';

        return view('contributions.index', compact('contributions', 'total', 'isAdmin'));
    }

    public function create()
    {
        $this->assertIsOrgAdmin();
        $orgId = $this->currentMembership()->organization_id;

        $members = Member::where('organization_id', $orgId)->where('status', 'approved')->orderBy('name')->get();

        return view('contributions.create', compact('members'));
    }

    public function store(Request $request)
    {
        $this->assertIsOrgAdmin();
        $orgId = $this->currentMembership()->organization_id;

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|string|max:100',
            'note' => 'nullable|string',
            'contributed_at' => 'required|date',
        ]);

        $member = Member::findOrFail($validated['member_id']);
        abort_unless($member->organization_id === $orgId, 403);

        $validated['organization_id'] = $orgId;

        Contribution::create($validated);

        return redirect()->route('contributions.index')->with('success', 'Contribution recorded successfully.');
    }

    public function edit(Contribution $contribution)
    {
        $this->assertIsOrgAdmin();
        abort_unless($contribution->organization_id === $this->currentMembership()->organization_id, 403);

        $orgId = $contribution->organization_id;
        $members = Member::where('organization_id', $orgId)->where('status', 'approved')->orderBy('name')->get();

        return view('contributions.edit', compact('contribution', 'members'));
    }

    public function update(Request $request, Contribution $contribution)
    {
        $this->assertIsOrgAdmin();
        abort_unless($contribution->organization_id === $this->currentMembership()->organization_id, 403);

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|string|max:100',
            'note' => 'nullable|string',
            'contributed_at' => 'required|date',
        ]);

        $member = Member::findOrFail($validated['member_id']);
        abort_unless($member->organization_id === $contribution->organization_id, 403);

        $contribution->update($validated);

        return redirect()->route('contributions.index')->with('success', 'Contribution updated successfully.');
    }

    public function destroy(Contribution $contribution)
    {
        $this->assertIsOrgAdmin();
        abort_unless($contribution->organization_id === $this->currentMembership()->organization_id, 403);

        $contribution->delete();

        return redirect()->route('contributions.index')->with('success', 'Contribution deleted.');
    }
}
