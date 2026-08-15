$path = "resources\views\layouts\navigation.blade.php"
$content = Get-Content $path -Raw

$desktopAnchor = '                        <x-dropdown-link :href="route(''profile.edit'')">{{ __(''Profile'') }}</x-dropdown-link>'
$desktopReplacement = @"
                        <x-dropdown-link :href="route('organizations.index')">Browse Organizations</x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
"@

$mobileAnchor = '                <a href="{{ route(''profile.edit'') }}" class="block text-sand-100">Profile</a>'
$mobileReplacement = @"
                <a href="{{ route('organizations.index') }}" class="block text-sand-100">Browse Organizations</a>
                <a href="{{ route('profile.edit') }}" class="block text-sand-100">Profile</a>
"@

if ($content -notmatch [regex]::Escape('Browse Organizations')) {
    $idx1 = $content.IndexOf($desktopAnchor)
    if ($idx1 -ge 0) {
        $content = $content.Substring(0, $idx1) + $desktopReplacement + $content.Substring($idx1 + $desktopAnchor.Length)
    } else {
        Write-Host "ERROR: desktop anchor not found."
    }

    $idx2 = $content.IndexOf($mobileAnchor)
    if ($idx2 -ge 0) {
        $content = $content.Substring(0, $idx2) + $mobileReplacement + $content.Substring($idx2 + $mobileAnchor.Length)
    } else {
        Write-Host "ERROR: mobile anchor not found."
    }

    [System.IO.File]::WriteAllText("$PWD\$path", $content, (New-Object System.Text.UTF8Encoding $false))
    Write-Host "Nav patched."
} else {
    Write-Host "Already patched, skipping."
}