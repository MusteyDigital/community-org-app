<?php

namespace App\Http\Controllers;

use App\Models\EventItem;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventItemController extends Controller
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

        abort_unless($membership->role === 'admin', 403, 'Only organization admins can manage events.');
    }

    public function index()
    {
        $orgId = $this->currentMembership()->organization_id;

        $events = EventItem::where('organization_id', $orgId)->orderBy('event_date')->paginate(10);

        return view('events.index', compact('events'));
    }

    public function create()
    {
        $this->assertIsOrgAdmin();

        return view('events.create');
    }

    public function store(Request $request)
    {
        $this->assertIsOrgAdmin();
        $orgId = $this->currentMembership()->organization_id;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'event_time' => 'nullable',
            'location' => 'nullable|string|max:255',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['organization_id'] = $orgId;

        EventItem::create($validated);

        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    public function edit(EventItem $event)
    {
        $this->assertIsOrgAdmin();
        abort_unless($event->organization_id === $this->currentMembership()->organization_id, 403);

        return view('events.edit', compact('event'));
    }

    public function update(Request $request, EventItem $event)
    {
        $this->assertIsOrgAdmin();
        abort_unless($event->organization_id === $this->currentMembership()->organization_id, 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'event_time' => 'nullable',
            'location' => 'nullable|string|max:255',
        ]);

        $event->update($validated);

        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(EventItem $event)
    {
        $this->assertIsOrgAdmin();
        abort_unless($event->organization_id === $this->currentMembership()->organization_id, 403);

        $event->delete();

        return redirect()->route('events.index')->with('success', 'Event deleted.');
    }
}
