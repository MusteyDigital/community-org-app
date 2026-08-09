<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::approved()->withCount('members')->get();

        return view('organizations.index', compact('organizations'));
    }

    public function create()
    {
        $existing = Member::where('user_id', Auth::id())->first();

        if ($existing) {
            return redirect()->route('dashboard')->with('status', 'You already belong to an organization.');
        }

        return view('organizations.create');
    }

    public function store(Request $request)
    {
        $existing = Member::where('user_id', Auth::id())->first();

        if ($existing) {
            return redirect()->route('dashboard')->with('status', 'You already belong to an organization.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:church,mosque,community',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            $organization = Organization::create([
                ...$validated,
                'created_by' => Auth::id(),
                'status' => 'pending',
            ]);

            Member::create([
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'user_id' => Auth::id(),
                'organization_id' => $organization->id,
                'role' => 'admin',
                'status' => 'approved',
                'join_date' => now(),
            ]);
        });

        return redirect()->route('dashboard')->with('status', 'Organization submitted for review. You will be notified once approved.');
    }

    public function join(Organization $organization)
    {
        $existing = Member::where('user_id', Auth::id())->first();

        if ($existing) {
            return back()->with('status', 'You already belong to or have a pending request with an organization.');
        }

        Member::create([
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'user_id' => Auth::id(),
            'organization_id' => $organization->id,
            'role' => 'member',
            'status' => 'pending',
            'join_date' => now(),
        ]);

        return redirect()->route('dashboard')->with('status', 'Request sent — waiting for admin approval.');
    }
}


