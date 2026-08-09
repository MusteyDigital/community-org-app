<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\EventItem;
use App\Models\Announcement;

class PublicOrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::approved()->orderBy('name')->take(6)->get();
        $organizationCount = Organization::approved()->count();
        return view('welcome', compact('organizations', 'organizationCount'));
    }

    public function directory()
    {
        $organizations = Organization::approved()->orderBy('name')->get();
        return view('organizations-directory', compact('organizations'));
    }

    public function show(string $slug)
    {
        $organization = Organization::approved()->where('slug', $slug)->firstOrFail();

        $upcomingEvents = EventItem::where('organization_id', $organization->id)
            ->where('event_date', '>=', now())
            ->orderBy('event_date')
            ->take(6)
            ->get();

        $pinnedAnnouncements = Announcement::where('organization_id', $organization->id)
            ->where('is_pinned', true)
            ->latest('published_at')
            ->take(5)
            ->get();

        return view('organization-public', compact('organization', 'upcomingEvents', 'pinnedAnnouncements'));
    }
}
