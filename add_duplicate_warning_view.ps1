$path = "resources\views\announcements\create.blade.php"
$content = Get-Content $path -Raw

$anchor = "                    @csrf"
$replacement = @"
                    @csrf
                    @if (session('duplicate_warning'))
                        <div class="mb-4 bg-gold-50 border border-gold-300 text-clay-700 text-sm rounded-lg px-4 py-3">
                            {{ session('duplicate_warning') }}
                            <input type="hidden" name="confirm_duplicate" value="1">
                        </div>
                    @endif
"@

$idx = $content.IndexOf($anchor)
if ($idx -ge 0) {
    $newContent = $content.Substring(0, $idx) + $replacement + $content.Substring($idx + $anchor.Length)
    [System.IO.File]::WriteAllText("$PWD\$path", $newContent, (New-Object System.Text.UTF8Encoding $false))
    Write-Host "View patched successfully."
} else {
    Write-Host "ERROR: anchor not found, no changes made."
}