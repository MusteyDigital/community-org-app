<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsMember(Organization $org, array $attrs = []): array
    {
        $user = User::factory()->create();
        $member = Member::factory()->create(array_merge([
            'user_id' => $user->id,
            'organization_id' => $org->id,
        ], $attrs));
        $this->actingAs($user);
        return [$user, $member];
    }

    public function test_index_lists_only_members_of_current_users_organization()
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        [$user, $member] = $this->actingAsMember($orgA, ['role' => 'admin']);
        Member::factory()->count(2)->create(['organization_id' => $orgA->id]);
        Member::factory()->count(3)->create(['organization_id' => $orgB->id]);

        $response = $this->get(route('members.index'));

        $response->assertOk();
        $response->assertViewHas('members', function ($members) use ($orgA) {
            return $members->every(fn ($m) => $m->organization_id === $orgA->id);
        });
    }

    public function test_index_search_filters_by_name_or_email()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        Member::factory()->create(['organization_id' => $org->id, 'name' => 'Zainab Bello', 'email' => 'zainab@example.com']);
        Member::factory()->create(['organization_id' => $org->id, 'name' => 'John Doe', 'email' => 'john@example.com']);

        $response = $this->get(route('members.index', ['search' => 'Zainab']));

        $response->assertOk();
        $response->assertViewHas('members', function ($members) {
            return $members->count() === 1 && $members->first()->name === 'Zainab Bello';
        });
    }

    public function test_guest_without_membership_cannot_view_index()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('members.index'));

        $response->assertStatus(403);
    }

    public function test_non_admin_cannot_create_member()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'member']);

        $response = $this->get(route('members.create'));

        $response->assertStatus(403);
    }

    public function test_admin_can_store_new_member()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);

        $response = $this->post(route('members.store'), [
            'name' => 'New Person',
            'email' => 'newperson@example.com',
            'phone' => '08012345678',
            'role' => 'member',
            'join_date' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('members.index'));
        $this->assertDatabaseHas('members', [
            'email' => 'newperson@example.com',
            'organization_id' => $org->id,
            'status' => 'approved',
        ]);
    }

    public function test_store_requires_unique_email()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        Member::factory()->create(['email' => 'taken@example.com']);

        $response = $this->post(route('members.store'), [
            'name' => 'New Person',
            'email' => 'taken@example.com',
            'role' => 'member',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_admin_cannot_edit_member_from_another_organization()
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        [$user] = $this->actingAsMember($orgA, ['role' => 'admin']);
        $foreignMember = Member::factory()->create(['organization_id' => $orgB->id]);

        $response = $this->get(route('members.edit', $foreignMember));

        $response->assertStatus(403);
    }

    public function test_admin_can_update_member()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        $member = Member::factory()->create(['organization_id' => $org->id]);

        $response = $this->put(route('members.update', $member), [
            'name' => 'Updated Name',
            'email' => $member->email,
            'role' => 'member',
        ]);

        $response->assertRedirect(route('members.index'));
        $this->assertDatabaseHas('members', ['id' => $member->id, 'name' => 'Updated Name']);
    }

    public function test_admin_can_delete_member()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        $member = Member::factory()->create(['organization_id' => $org->id]);

        $response = $this->delete(route('members.destroy', $member));

        $response->assertRedirect(route('members.index'));
        $this->assertDatabaseMissing('members', ['id' => $member->id]);
    }

    public function test_directory_only_shows_approved_and_listed_members()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org);
        Member::factory()->create(['organization_id' => $org->id, 'status' => 'approved', 'is_listed' => true, 'name' => 'Visible Person']);
        Member::factory()->create(['organization_id' => $org->id, 'status' => 'approved', 'is_listed' => false, 'name' => 'Hidden Person']);
        Member::factory()->create(['organization_id' => $org->id, 'status' => 'pending', 'is_listed' => true, 'name' => 'Pending Person']);

        $response = $this->get(route('members.directory'));

        $response->assertOk();
        $response->assertViewHas('members', function ($members) {
            $names = $members->pluck('name')->all();
            return in_array('Visible Person', $names)
                && ! in_array('Hidden Person', $names)
                && ! in_array('Pending Person', $names);
        });
    }

    public function test_member_can_update_own_visibility()
    {
        $org = Organization::factory()->create();
        [$user, $member] = $this->actingAsMember($org, ['is_listed' => false]);

        $response = $this->post(route('members.visibility'), ['is_listed' => true]);

        $response->assertRedirect();
        $this->assertDatabaseHas('members', ['id' => $member->id, 'is_listed' => true]);
    }

    public function test_admin_can_approve_pending_member()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        $pending = Member::factory()->create(['organization_id' => $org->id, 'status' => 'pending']);

        $response = $this->post(route('members.approve', $pending));

        $response->assertRedirect();
        $this->assertDatabaseHas('members', ['id' => $pending->id, 'status' => 'approved']);
    }

    public function test_admin_can_reject_pending_member()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        $pending = Member::factory()->create(['organization_id' => $org->id, 'status' => 'pending']);

        $response = $this->post(route('members.reject', $pending));

        $response->assertRedirect();
        $this->assertDatabaseHas('members', ['id' => $pending->id, 'status' => 'rejected']);
    }

    public function test_non_admin_cannot_approve_member()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'member']);
        $pending = Member::factory()->create(['organization_id' => $org->id, 'status' => 'pending']);

        $response = $this->post(route('members.approve', $pending));

        $response->assertStatus(403);
    }
}