$path = "routes\web.php"
$content = Get-Content $path -Raw

$anchor = "    Route::post('/members/{member}/reject', [MemberController::class, 'reject'])->name('members.reject');"
$newRoute = $anchor + "`n    Route::post('/members/{member}/visibility', [MemberController::class, 'adminUpdateVisibility'])->name('members.adminVisibility');"

$content = $content -replace [regex]::Escape($anchor), $newRoute
[System.IO.File]::WriteAllText("$PWD\$path", $content, (New-Object System.Text.UTF8Encoding $false))
Write-Host "Route added."