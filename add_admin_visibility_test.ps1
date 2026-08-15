$path = "tests\Feature\MemberControllerTest.php"
$content = Get-Content $path -Raw

$anchor = "    public function test_non_admin_cannot_approve_member()"
$newTests = @'
    public function test_admin_can_update_another_members_visibility()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'admin']);
        $member = Member::factory()->create(['organization_id' => $org->id, 'is_listed' => false]);

        $response = $this->post(route('members.adminVisibility', $member), ['is_listed' => true]);

        $response->assertRedirect();
        $this->assertDatabaseHas('members', ['id' => $member->id, 'is_listed' => true]);
    }

    public function test_non_admin_cannot_update_another_members_visibility()
    {
        $org = Organization::factory()->create();
        [$user] = $this->actingAsMember($org, ['role' => 'member']);
        $member = Member::factory()->create(['organization_id' => $org->id, 'is_listed' => false]);

        $response = $this->post(route('members.adminVisibility', $member), ['is_listed' => true]);

        $response->assertStatus(403);
    }

    public function test_admin_cannot_update_visibility_for_member_in_another_organization()
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        [$user] = $this->actingAsMember($orgA, ['role' => 'admin']);
        $foreignMember = Member::factory()->create(['organization_id' => $orgB->id, 'is_listed' => false]);

        $response = $this->post(route('members.adminVisibility', $foreignMember), ['is_listed' => true]);

        $response->assertStatus(403);
    }

'@ + $anchor

$content = $content -replace [regex]::Escape($anchor), $newTests
[System.IO.File]::WriteAllText("$PWD\$path", $content, (New-Object System.Text.UTF8Encoding $false))
Write-Host "Tests added."