$path = "tests\Feature\AnnouncementControllerTest.php"
$content = Get-Content $path -Raw

$anchor = "    public function test_admin_cannot_update_announcement_from_another_organization()"
$newTests = @'
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

'@ + $anchor

$content = $content -replace [regex]::Escape($anchor), $newTests
[System.IO.File]::WriteAllText("$PWD\$path", $content, (New-Object System.Text.UTF8Encoding $false))
Write-Host "Tests added."