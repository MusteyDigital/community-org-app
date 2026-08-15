$path = "app\Http\Controllers\AnnouncementController.php"
$content = Get-Content $path -Raw

$anchor = @'
        $validated['body'] = str_replace("\r\n", "\n", $validated['body']);
        $validated['is_pinned'] = $request->has('is_pinned');
        $validated['published_at'] = $validated['published_at'] ?? now();
        $validated['organization_id'] = $orgId;
        $announcement = Announcement::create($validated);
'@

$replacement = @'
        $validated['body'] = str_replace("\r\n", "\n", $validated['body']);
        $validated['is_pinned'] = $request->has('is_pinned');
        $validated['published_at'] = $validated['published_at'] ?? now();
        $validated['organization_id'] = $orgId;

        $possibleDuplicate = Announcement::where('organization_id', $orgId)
            ->where('title', $validated['title'])
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if ($possibleDuplicate && ! $request->boolean('confirm_duplicate')) {
            return back()->withInput()->with('duplicate_warning', 'An announcement with this title was already posted in the last 24 hours. Submit again to post it anyway.');
        }

        $announcement = Announcement::create($validated);
'@

$content = $content -replace [regex]::Escape($anchor), $replacement
[System.IO.File]::WriteAllText("$PWD\$path", $content, (New-Object System.Text.UTF8Encoding $false))
Write-Host "AnnouncementController patched."