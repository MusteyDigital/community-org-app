function Add-HasFactory($path) {
    $content = Get-Content $path -Raw
    if ($content -notmatch 'HasFactory') {
        $content = $content -replace '(use Illuminate\\Database\\Eloquent\\Model;)', "`$1`nuse Illuminate\Database\Eloquent\Factories\HasFactory;"
        $content = $content -replace '(class \w+ extends Model\s*\r?\n\{)', "`$1`n    use HasFactory;"
        [System.IO.File]::WriteAllText($path, $content, (New-Object System.Text.UTF8Encoding $false))
        Write-Host "Patched: $path"
    } else {
        Write-Host "Already has HasFactory, skipped: $path"
    }
}

Add-HasFactory "app\Models\Organization.php"
Add-HasFactory "app\Models\Member.php"
Add-HasFactory "app\Models\Announcement.php"