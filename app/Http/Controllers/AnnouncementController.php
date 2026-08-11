<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Member;
use App\Notifications\AnnouncementPosted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AnnouncementController extends Controller
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
        abort_unless($membership->role === 'admin', 403, 'Only organization admins can manage announcements.');
    }

    public function index(Request $request)
    {
        $orgId = $this->currentMembership()->organization_id;

        $announcements = Announcement::where('organization_id', $orgId)
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($q2) => $q2
                ->where('title', 'like', '%'.$request->search.'%')
                ->orWhere('body', 'like', '%'.$request->search.'%')
            ))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('pinned'), fn ($q) => $q->where('is_pinned', $request->pinned === '1'))
            ->orderByDesc('is_pinned')
            ->latest('published_at')
            ->paginate(10)
            ->withQueryString();

        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        $this->assertIsOrgAdmin();
        return view('announcements.create');
    }

    public function store(Request $request)
    {
        $this->assertIsOrgAdmin();
        $orgId = $this->currentMembership()->organization_id;
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'is_pinned' => 'nullable|boolean',
            'published_at' => 'nullable|date',
            'type' => 'required|in:general,burial',
        ]);
        $validated['is_pinned'] = $request->has('is_pinned');
        $validated['published_at'] = $validated['published_at'] ?? now();
        $validated['organization_id'] = $orgId;
        $announcement = Announcement::create($validated);

        $recipients = Member::where('organization_id', $orgId)
            ->where('status', 'approved')
            ->whereNotNull('user_id')
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter();

        if ($recipients->isNotEmpty()) {
            try {
                Notification::send($recipients, new AnnouncementPosted($announcement));
            } catch (\Throwable $e) {
                Log::warning('Failed to send announcement notification: '.$e->getMessage());
            }
        }

        return redirect()->route('announcements.index')->with('success', 'Announcement posted successfully.');
    }

    public function edit(Announcement $announcement)
    {
        $this->assertIsOrgAdmin();
        abort_unless($announcement->organization_id === $this->currentMembership()->organization_id, 403);
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $this->assertIsOrgAdmin();
        abort_unless($announcement->organization_id === $this->currentMembership()->organization_id, 403);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'is_pinned' => 'nullable|boolean',
            'published_at' => 'nullable|date',
            'type' => 'required|in:general,burial',
        ]);
        $validated['is_pinned'] = $request->has('is_pinned');
        $announcement->update($validated);
        return redirect()->route('announcements.index')->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $this->assertIsOrgAdmin();
        abort_unless($announcement->organization_id === $this->currentMembership()->organization_id, 403);
        $announcement->delete();
        return redirect()->route('announcements.index')->with('success', 'Announcement deleted.');
    }
}
