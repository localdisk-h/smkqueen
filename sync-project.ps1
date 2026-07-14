[CmdletBinding()]
param(
    [string]$Message = "Sync Queen Al-Falah project updates"
)

$ErrorActionPreference = "Stop"
$repoRoot = (Resolve-Path -LiteralPath $PSScriptRoot).Path
$workspaceRoot = (Resolve-Path -LiteralPath (Join-Path $repoRoot "..\..")).Path
$sources = @(
    @{
        Source = Join-Path $workspaceRoot "work\queen-alfalah"
        Target = Join-Path $repoRoot "queen-alfalah"
        Marker = "style.css"
    },
    @{
        Source = Join-Path $workspaceRoot "work\queen-alfalah-core"
        Target = Join-Path $repoRoot "queen-alfalah-core"
        Marker = "queen-alfalah-core.php"
    }
)

Push-Location $repoRoot
try {
    $remote = git remote get-url origin 2>$null
    if (-not $remote) {
        git remote add origin "https://github.com/localdisk-h/smkqueen.git"
    }

    # The GitHub repository was initially bootstrapped through GitHub Actions.
    # On the first successful networked run, align this working copy to that
    # history while retaining the previous local history on a backup branch.
    git fetch origin main
    if ($LASTEXITCODE -ne 0) {
        throw "Fetch gagal. Pastikan internet tersedia dan GitHub sudah terautentikasi."
    }

    git show-ref --verify --quiet refs/remotes/origin/main
    if ($LASTEXITCODE -eq 0) {
        git merge-base HEAD origin/main *> $null
        if ($LASTEXITCODE -ne 0) {
            $dirty = git status --porcelain
            if ($dirty) {
                throw "Repository memiliki perubahan lokal yang belum dicatat. Commit atau simpan perubahan tersebut sebelum sinkronisasi pertama."
            }

            $stamp = Get-Date -Format "yyyyMMdd-HHmmss"
            $backupBranch = "local-history-backup-$stamp"
            $temporaryBranch = "github-sync-$stamp"
            git branch $backupBranch HEAD
            git switch --create $temporaryBranch --track origin/main
            git branch -D main
            git branch -m main
            git branch --set-upstream-to=origin/main main
            Write-Host "Riwayat lokal lama disimpan pada branch: $backupBranch"
        }
    }

    foreach ($item in $sources) {
        $source = [System.IO.Path]::GetFullPath($item.Source)
        $target = [System.IO.Path]::GetFullPath($item.Target)

        if (-not (Test-Path -LiteralPath (Join-Path $source $item.Marker))) {
            throw "Sumber project tidak valid: $source"
        }
        if (-not $target.StartsWith($repoRoot + [System.IO.Path]::DirectorySeparatorChar, [System.StringComparison]::OrdinalIgnoreCase)) {
            throw "Target sinkronisasi berada di luar repository: $target"
        }

        New-Item -ItemType Directory -Path $target -Force | Out-Null
        & robocopy.exe $source $target /MIR /R:2 /W:1 /XD .git node_modules vendor /XF *.zip *.log | Out-Host
        if ($LASTEXITCODE -ge 8) {
            throw "Sinkronisasi gagal untuk $source (kode Robocopy $LASTEXITCODE)."
        }
    }

    git add --all
    git diff --cached --quiet
    if ($LASTEXITCODE -eq 0) {
        Write-Host "Tidak ada perubahan baru untuk dikirim."
        exit 0
    }

    git commit -m $Message
    if ($LASTEXITCODE -ne 0) { throw "Commit Git gagal dibuat." }

    git push -u origin main
    if ($LASTEXITCODE -ne 0) {
        throw "Push gagal. Pastikan internet tersedia dan GitHub sudah terautentikasi. Commit lokal tetap aman."
    }

    Write-Host "Sinkronisasi selesai: https://github.com/localdisk-h/smkqueen"
} finally {
    Pop-Location
}
