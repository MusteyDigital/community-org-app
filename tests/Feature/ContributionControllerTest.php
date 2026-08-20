<?php

namespace Tests\Feature;

use App\Models\Contribution;
use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContributionControllerTest extends TestCase
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

    public function test_admin_index_lists_only_contributions_of_current_organization()
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        [$user] = $this->actingAsMember($orgA, ['role' => 'admin']);
        Contribution::factory()->count(2)->create(['organization_id' => $orgA->id]);
        Contribution::factory()->count(3)->create(['organization_id' => $orgB->id]);

        $response = $this->get(route('contributions.index'));

        $response->assertOk();
        $response->assertViewHas('contributions', function ($contributions) use ($orgA) {
            return $contributions->every(fn ($c) => $c->organization_id === $orgA->id);
        });
    }

    public function test_non_admin_only_sees_own_contributions()
    {
        $org = Organization::factory()->create();
        [$user, $member] = $this->actingAsMember($org, ['role' => 'member']);
        $otherMember = Member::factory()->create(['organization_id' => $org->id]);
        Contribution::factory()->create(['organization_id' => $org->id, 'member_id' => $member->id]);
        Contribution::factory()->create(['organization_id' => $org->id, 'member_id' => $otherMember->id]);

        $response = $this->get(route('contributions.index'));

        $response->assertOk();
        $response->assertViewHas('contributions', function ($contributions) use ($member) {
            return $contributions->every(fn ($c) => $c->member_id === $member->id);
        });
    }

    public function test_non_member_cannot_view_index()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('contributions.index'));

        $response->assertStatus(403);
    }

    public function test_non_admin_cannot_create_contribution()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'member']);

        $response = $this->get(route('contributions.create'));

        $response->assertStatus(403);
    }

    public function test_admin_can_store_contribution_with_normalized_line_endings()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        $member = Member::factory()->create(['organization_id' => $org->id, 'status' => 'approved']);

        $response = $this->post(route('contributions.store'), [
            'member_id' => $member->id,
            'amount' => 5000,
            'category' => 'zakat',
            'note' => "Line one\r\nLine two",
            'contributed_at' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('contributions.index'));
        $this->assertDatabaseHas('contributions', [
            'member_id' => $member->id,
            'organization_id' => $org->id,
        ]);
        $contribution = Contribution::where('member_id', $member->id)->first();
        $this->assertStringNotContainsString("\r\n", $contribution->note);
    }

    public function test_admin_cannot_store_contribution_for_member_in_another_organization()
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        [$user] = $this->actingAsMember($orgA, ['role' => 'admin']);
        $foreignMember = Member::factory()->create(['organization_id' => $orgB->id, 'status' => 'approved']);

        $response = $this->post(route('contributions.store'), [
            'member_id' => $foreignMember->id,
            'amount' => 5000,
            'category' => 'zakat',
            'contributed_at' => now()->toDateString(),
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_cannot_edit_contribution_from_another_organization()
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        [$user] = $this->actingAsMember($orgA, ['role' => 'admin']);
        $foreignContribution = Contribution::factory()->create(['organization_id' => $orgB->id]);

        $response = $this->get(route('contributions.edit', $foreignContribution));

        $response->assertStatus(403);
    }

    public function test_admin_can_update_contribution()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        $member = Member::factory()->create(['organization_id' => $org->id, 'status' => 'approved']);
        $contribution = Contribution::factory()->create(['organization_id' => $org->id, 'member_id' => $member->id]);

        $response = $this->put(route('contributions.update', $contribution), [
            'member_id' => $member->id,
            'amount' => 9999,
            'category' => 'sadaqah',
            'contributed_at' => $contribution->contributed_at->toDateString(),
        ]);

        $response->assertRedirect(route('contributions.index'));
        $this->assertDatabaseHas('contributions', ['id' => $contribution->id, 'amount' => 9999]);
    }

    public function test_admin_can_delete_contribution()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        $contribution = Contribution::factory()->create(['organization_id' => $org->id]);

        $response = $this->delete(route('contributions.destroy', $contribution));

        $response->assertRedirect(route('contributions.index'));
        $this->assertSoftDeleted('contributions', ['id' => $contribution->id]);
    }

    public function test_admin_cannot_delete_contribution_from_another_organization()
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        [$user] = $this->actingAsMember($orgA, ['role' => 'admin']);
        $foreignContribution = Contribution::factory()->create(['organization_id' => $orgB->id]);

        $response = $this->delete(route('contributions.destroy', $foreignContribution));

        $response->assertStatus(403);
        $this->assertDatabaseHas('contributions', ['id' => $foreignContribution->id]);
    }
}
