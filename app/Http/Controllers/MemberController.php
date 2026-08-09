<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    private function currentOrgId()
    {
        $membership = Member::where('user_id', Auth::id())->first();

        abort_unless($membership, 403, 'You do not belong to any organization.');

        return $membership->organization_id;
    }

    private function assertIsOrgAdmin()
    {
        $membership = Member::where('user_id', Auth::id())->first();

        abort_unless($membership && $membership->role === 'admin' && $membership->status === 'approved', 403, 'Only organization admins can manage members.');
    }

    public function index()
    {
        $orgId = $this->currentOrgId();

        $members = Member::where('organization_id', $orgId)->latest()->paginate(10);

        return view('members.index', compact('members'));
    }

    public function create()
    {
        $this->assertIsOrgAdmin();

        return view('members.create');
    }

    public function store(Request $request)
    {
        $this->assertIsOrgAdmin();
        $orgId = $this->currentOrgId();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:member,admin',
            'join_date' => 'nullable|date',
        ]);

        Member::create([
            ...$validated,
            'organization_id' => $orgId,
            'status' => 'approved',
        ]);

        return redirect()->route('members.index')->with('success', 'Member added successfully.');
    }

    public function edit(Member $member)
    {
        $this->assertIsOrgAdmin();
        abort_unless($member->organization_id === $this->currentOrgId(), 403);

        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $this->assertIsOrgAdmin();
        abort_unless($member->organization_id === $this->currentOrgId(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email,' . $member->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:member,admin',
            'join_date' => 'nullable|date',
        ]);

        $member->update($validated);

        return redirect()->route('members.index')->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        $this->assertIsOrgAdmin();
        abort_unless($member->organization_id === $this->currentOrgId(), 403);

        $member->delete();

        return redirect()->route('members.index')->with('success', 'Member deleted.');
    }

    public function approve(Member $member)
    {
        $this->assertIsOrgAdmin();
        abort_unless($member->organization_id === $this->currentOrgId(), 403);

        $member->update(['status' => 'approved']);

        return back()->with('success', "{$member->name} approved.");
    }

    public function reject(Member $member)
    {
        $this->assertIsOrgAdmin();
        abort_unless($member->organization_id === $this->currentOrgId(), 403);

        $member->update(['status' => 'rejected']);

        return back()->with('success', "{$member->name} rejected.");
    }
}
