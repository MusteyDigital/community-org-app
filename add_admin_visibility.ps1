$path = "app\Http\Controllers\MemberController.php"
$content = Get-Content $path -Raw

$anchor = "    public function reject(Member `$member)"
$newMethod = @'
    public function adminUpdateVisibility(Request $request, Member $member)
    {
        $this->assertIsOrgAdmin();
        abort_unless($member->organization_id === $this->currentOrgId(), 403);
        $request->validate(['is_listed' => 'required|boolean']);
        $member->update(['is_listed' => $request->boolean('is_listed')]);
        return back()->with('success', "{$member->name}'s directory visibility updated.");
    }

'@ + $anchor

$content = $content -replace [regex]::Escape($anchor), $newMethod
[System.IO.File]::WriteAllText("$PWD\$path", $content, (New-Object System.Text.UTF8Encoding $false))
Write-Host "MemberController patched."