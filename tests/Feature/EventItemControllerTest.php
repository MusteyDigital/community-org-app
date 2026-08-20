<?php

namespace Tests\Feature;

use App\Models\EventItem;
use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventItemControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsMember(Organization $org, array $attrs = []): array
    {
        $user = User::factory()->create();
        $member = Member::factory()->create(array_merge([
            'user_id' => $user->id,
            'organization_id' => $org->id,
            'status' => 'approved',
        ], $attrs));
        $this->actingAs($user);
        return [$user, $member];
    }

    public function test_index_lists_only_events_of_current_users_organization()
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        [$user] = $this->actingAsMember($orgA, ['role' => 'admin']);
        EventItem::factory()->count(2)->create(['organization_id' => $orgA->id]);
        EventItem::factory()->count(3)->create(['organization_id' => $orgB->id]);

        $response = $this->get(route('events.index'));

        $response->assertOk();
        $response->assertViewHas('events', function ($events) use ($orgA) {
            return $events->every(fn ($e) => $e->organization_id === $orgA->id);
        });
    }

    public function test_index_search_filters_by_title_or_location()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        EventItem::factory()->create(['organization_id' => $org->id, 'title' => 'Jumuah Prayer']);
        EventItem::factory()->create(['organization_id' => $org->id, 'title' => 'Car Park Maintenance']);

        $response = $this->get(route('events.index', ['search' => 'Jumuah']));

        $response->assertOk();
        $response->assertViewHas('events', function ($events) {
            return $events->count() === 1 && str_contains($events->first()->title, 'Jumuah');
        });
    }

    public function test_non_member_cannot_view_index()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('events.index'));

        $response->assertStatus(403);
    }

    public function test_non_admin_cannot_create_event()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'member']);

        $response = $this->get(route('events.create'));

        $response->assertStatus(403);
    }

    public function test_admin_can_store_event_with_normalized_line_endings()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);

        $response = $this->post(route('events.store'), [
            'title' => 'Test Event',
            'description' => "Line one\r\nLine two",
            'event_date' => now()->addDays(3)->toDateString(),
            'location' => 'Main Hall',
        ]);

        $response->assertRedirect(route('events.index'));
        $this->assertDatabaseHas('event_items', [
            'title' => 'Test Event',
            'organization_id' => $org->id,
        ]);
        $event = EventItem::where('title', 'Test Event')->first();
        $this->assertStringNotContainsString("\r\n", $event->description);
    }

    public function test_admin_cannot_edit_event_from_another_organization()
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        [$user] = $this->actingAsMember($orgA, ['role' => 'admin']);
        $foreignEvent = EventItem::factory()->create(['organization_id' => $orgB->id]);

        $response = $this->get(route('events.edit', $foreignEvent));

        $response->assertStatus(403);
    }

    public function test_admin_can_update_event()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        $event = EventItem::factory()->create(['organization_id' => $org->id]);

        $response = $this->put(route('events.update', $event), [
            'title' => 'Updated Title',
            'description' => $event->description,
            'event_date' => $event->event_date,
            'location' => $event->location,
        ]);

        $response->assertRedirect(route('events.index'));
        $this->assertDatabaseHas('event_items', ['id' => $event->id, 'title' => 'Updated Title']);
    }

    public function test_admin_cannot_update_event_from_another_organization()
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        [$user] = $this->actingAsMember($orgA, ['role' => 'admin']);
        $foreignEvent = EventItem::factory()->create(['organization_id' => $orgB->id]);

        $response = $this->put(route('events.update', $foreignEvent), [
            'title' => 'Hijacked Title',
            'event_date' => now()->addDay()->toDateString(),
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_event()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        $event = EventItem::factory()->create(['organization_id' => $org->id]);

        $response = $this->delete(route('events.destroy', $event));

        $response->assertRedirect(route('events.index'));
        $this->assertDatabaseMissing('event_items', ['id' => $event->id]);
    }

    public function test_admin_cannot_delete_event_from_another_organization()
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        [$user] = $this->actingAsMember($orgA, ['role' => 'admin']);
        $foreignEvent = EventItem::factory()->create(['organization_id' => $orgB->id]);

        $response = $this->delete(route('events.destroy', $foreignEvent));

        $response->assertStatus(403);
        $this->assertDatabaseHas('event_items', ['id' => $foreignEvent->id]);
    }
}
