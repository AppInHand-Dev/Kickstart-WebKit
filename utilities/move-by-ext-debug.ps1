[CmdletBinding()]
param(
  [string]$Root = (Get-Location).Path,
  [Parameter(Mandatory=$true)][string]$Target,
  [Parameter(Mandatory=$true)][string]$Extensions,
  [switch]$DryRun
)

function Normalize-Extensions { param([string]$exts) ($exts -split '[,; ]+' | ForEach-Object { $_.Trim().TrimStart('.') } | Where-Object { $_ -ne "" } | ForEach-Object { $_.ToLower() }) }

$Extensions = Normalize-Extensions -exts $Extensions
Write-Host "Root: $Root"
Write-Host "Target: $Target"
Write-Host "Extensions: $($Extensions -join ', ')"
Write-Host "DryRun: $DryRun"
Write-Host ""

# 1) conta tutti i file
$all = Get-ChildItem -Path $Root -Recurse -File -ErrorAction SilentlyContinue
Write-Host "Totale file trovati sotto root: $($all.Count)"

# 2) filtra per estensioni
$pattern = $Extensions | ForEach-Object { "*.$_" }
$matched = @()
foreach ($p in $pattern) {
  $matched += Get-ChildItem -Path $Root -Recurse -File -Include $p -ErrorAction SilentlyContinue
}
$matched = $matched | Select-Object -Unique
Write-Host "File che corrispondono alle estensioni ($($Extensions -join ', ')): $($matched.Count)"
if ($matched.Count -gt 0) {
  Write-Host "Primi 20 file trovati:"
  $matched | Select-Object -First 20 FullName | ForEach-Object { Write-Host " - $_" }
} else {
  Write-Host "Nessun file corrispondente trovato. Controlla il percorso e le estensioni."
}

# Se DryRun, esci qui (non spostare)
if ($DryRun) { Write-Host "`nDryRun attivo: nessun file verrà spostato."; exit 0 }

# Se non DryRun, procedi con spostamento semplice (senza rinomina avanzata)
foreach ($f in $matched) {
  $ext = $f.Extension.TrimStart('.').ToLower()
  $destDir = Join-Path $Target $ext
  if (-not (Test-Path $destDir)) { New-Item -ItemType Directory -Path $destDir -Force | Out-Null }
  $dest = Join-Path $destDir $f.Name
  try {
    Move-Item -LiteralPath $f.FullName -Destination $dest -ErrorAction Stop
    Write-Host "OK: $($f.FullName) -> $dest"
  } catch {
    Write-Warning "Errore spostando $($f.FullName): $($_.Exception.Message)"
  }
}
Write-Host "Operazione completata."
