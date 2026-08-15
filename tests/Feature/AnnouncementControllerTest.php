<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AnnouncementControllerTest extends TestCase
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

    public function test_index_lists_only_announcements_of_current_users_organization()
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        [$user] = $this->actingAsMember($orgA, ['role' => 'admin']);
        Announcement::factory()->count(2)->create(['organization_id' => $orgA->id]);
        Announcement::factory()->count(3)->create(['organization_id' => $orgB->id]);

        $response = $this->get(route('announcements.index'));

        $response->assertOk();
        $response->assertViewHas('announcements', function ($announcements) use ($orgA) {
            return $announcements->every(fn ($a) => $a->organization_id === $orgA->id);
        });
    }

    public function test_index_pinned_announcements_appear_first()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        $unpinned = Announcement::factory()->create(['organization_id' => $org->id, 'is_pinned' => false, 'published_at' => now()]);
        $pinned = Announcement::factory()->create(['organization_id' => $org->id, 'is_pinned' => true, 'published_at' => now()->subDay()]);

        $response = $this->get(route('announcements.index'));

        $response->assertOk();
        $response->assertViewHas('announcements', function ($announcements) use ($pinned) {
            return $announcements->first()->id === $pinned->id;
        });
    }

    public function test_index_search_filters_by_title_or_body()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        Announcement::factory()->create(['organization_id' => $org->id, 'title' => 'Ramadan Program Schedule']);
        Announcement::factory()->create(['organization_id' => $org->id, 'title' => 'Car Park Maintenance']);

        $response = $this->get(route('announcements.index', ['search' => 'Ramadan']));

        $response->assertOk();
        $response->assertViewHas('announcements', function ($announcements) {
            return $announcements->count() === 1 && str_contains($announcements->first()->title, 'Ramadan');
        });
    }

    public function test_non_member_cannot_view_index()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('announcements.index'));

        $response->assertStatus(403);
    }

    public function test_non_admin_cannot_create_announcement()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'member']);

        $response = $this->get(route('announcements.create'));

        $response->assertStatus(403);
    }

    public function test_admin_can_store_announcement_and_notifications_are_sent()
    {
        Notification::fake();
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        Member::factory()->create(['organization_id' => $org->id, 'status' => 'approved', 'user_id' => User::factory()->create()->id]);

        $response = $this->post(route('announcements.store'), [
            'title' => 'Test Announcement',
            'body' => "Line one\r\nLine two",
            'type' => 'general',
        ]);

        $response->assertRedirect(route('announcements.index'));
        $this->assertDatabaseHas('announcements', [
            'title' => 'Test Announcement',
            'organization_id' => $org->id,
        ]);
        $announcement = Announcement::where('title', 'Test Announcement')->first();
        $this->assertStringNotContainsString("\r\n", $announcement->body);
        Notification::assertSentTimes(\App\Notifications\AnnouncementPosted::class, 2);
    }

    public function test_store_warns_on_likely_duplicate_within_24_hours()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        Announcement::factory()->create([
            'organization_id' => $org->id,
            'title' => 'Repeated Title',
            'created_at' => now()->subHours(2),
        ]);

        $response = $this->post(route('announcements.store'), [
            'title' => 'Repeated Title',
            'body' => 'Some new body text',
            'type' => 'general',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('duplicate_warning');
        $this->assertEquals(1, Announcement::where('title', 'Repeated Title')->count());
    }

    public function test_store_allows_duplicate_when_confirmed()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        Announcement::factory()->create([
            'organization_id' => $org->id,
            'title' => 'Repeated Title',
            'created_at' => now()->subHours(2),
        ]);

        $response = $this->post(route('announcements.store'), [
            'title' => 'Repeated Title',
            'body' => 'Some new body text',
            'type' => 'general',
            'confirm_duplicate' => 1,
        ]);

        $response->assertRedirect(route('announcements.index'));
        $this->assertEquals(2, Announcement::where('title', 'Repeated Title')->count());
    }

    public function test_store_does_not_warn_for_duplicate_title_older_than_24_hours()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        Announcement::factory()->create([
            'organization_id' => $org->id,
            'title' => 'Repeated Title',
            'created_at' => now()->subDays(2),
        ]);

        $response = $this->post(route('announcements.store'), [
            'title' => 'Repeated Title',
            'body' => 'Some new body text',
            'type' => 'general',
        ]);

        $response->assertRedirect(route('announcements.index'));
        $this->assertEquals(2, Announcement::where('title', 'Repeated Title')->count());
    }
    public function test_admin_cannot_update_announcement_from_another_organization()
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        [$user] = $this->actingAsMember($orgA, ['role' => 'admin']);
        $foreignAnnouncement = Announcement::factory()->create(['organization_id' => $orgB->id]);

        $response = $this->get(route('announcements.edit', $foreignAnnouncement));

        $response->assertStatus(403);
    }

    public function test_admin_can_soft_delete_announcement()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        $announcement = Announcement::factory()->create(['organization_id' => $org->id]);

        $response = $this->delete(route('announcements.destroy', $announcement));

        $response->assertRedirect(route('announcements.index'));
        $this->assertSoftDeleted('announcements', ['id' => $announcement->id]);
    }

    public function test_trashed_lists_only_soft_deleted_announcements_of_current_org()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        $deleted = Announcement::factory()->create(['organization_id' => $org->id]);
        $deleted->delete();
        Announcement::factory()->create(['organization_id' => $org->id]);

        $response = $this->get(route('announcements.trashed'));

        $response->assertOk();
        $response->assertViewHas('announcements', function ($announcements) use ($deleted) {
            return $announcements->count() === 1 && $announcements->first()->id === $deleted->id;
        });
    }

    public function test_admin_can_restore_soft_deleted_announcement()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        $announcement = Announcement::factory()->create(['organization_id' => $org->id]);
        $announcement->delete();

        $response = $this->post(route('announcements.restore', $announcement->id));

        $response->assertRedirect(route('announcements.trashed'));
        $this->assertDatabaseHas('announcements', ['id' => $announcement->id, 'deleted_at' => null]);
    }

    public function test_admin_cannot_restore_announcement_from_another_organization()
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        [$user] = $this->actingAsMember($orgA, ['role' => 'admin']);
        $foreignAnnouncement = Announcement::factory()->create(['organization_id' => $orgB->id]);
        $foreignAnnouncement->delete();

        $response = $this->post(route('announcements.restore', $foreignAnnouncement->id));

        $response->assertStatus(403);
    }

    public function test_admin_can_permanently_delete_announcement()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        $announcement = Announcement::factory()->create(['organization_id' => $org->id]);
        $announcement->delete();

        $response = $this->delete(route('announcements.forceDelete', $announcement->id));

        $response->assertRedirect(route('announcements.trashed'));
        $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
    }
}