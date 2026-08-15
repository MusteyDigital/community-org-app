$path = "app\Http\Controllers\AnnouncementController.php"
$content = Get-Content $path -Raw

$anchor = '        $announcement = Announcement::create($validated);'
$replacement = @"
        `$possibleDuplicate = Announcement::where('organization_id', `$orgId)
            ->where('title', `$validated['title'])
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if (`$possibleDuplicate && ! `$request->boolean('confirm_duplicate')) {
            return back()->withInput()->with('duplicate_warning', 'An announcement with this title was already posted in the last 24 hours. Submit again to post it anyway.');
        }

$anchor
"@

if ($content -notmatch [regex]::Escape($anchor)) {
    Write-Host "ERROR: anchor not found, no changes made."
} elseif ($content -match "possibleDuplicate") {
    Write-Host "Already patched, skipping."
} else {
    $content = $content -replace [regex]::Escape($anchor), [System.Text.RegularExpressions.Regex]::Escape($replacement).Replace('\','\\') 2>$null
    # Use a literal, non-regex replace instead to avoid escaping headaches
    $raw = Get-Content $path -Raw
    $idx = $raw.IndexOf($anchor)
    if ($idx -ge 0) {
        $newContent = $raw.Substring(0, $idx) + $replacement + $raw.Substring($idx + $anchor.Length)
        [System.IO.File]::WriteAllText("$PWD\$path", $newContent, (New-Object System.Text.UTF8Encoding $false))
        Write-Host "Patched successfully."
    } else {
        Write-Host "ERROR: anchor not found on second check."
    }
}