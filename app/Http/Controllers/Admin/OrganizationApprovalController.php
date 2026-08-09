<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationApprovalController extends Controller
{
    public function index()
    {
        $pending = Organization::where('status', 'pending')->with('creator')->latest()->get();
        $approved = Organization::where('status', 'approved')->with('creator')->latest()->get();
        $rejected = Organization::where('status', 'rejected')->with('creator')->latest()->get();

        return view('admin.organizations.index', compact('pending', 'approved', 'rejected'));
    }

    public function approve(Organization $organization)
    {
        $organization->update(['status' => 'approved']);

        return back()->with('status', "{$organization->name} approved.");
    }

    public function reject(Organization $organization)
    {
        $organization->update(['status' => 'rejected']);

        return back()->with('status', "{$organization->name} rejected.");
    }
}
