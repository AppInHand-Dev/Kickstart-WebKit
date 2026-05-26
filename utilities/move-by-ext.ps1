[CmdletBinding()]
param(
  [string]$Root = (Get-Location).Path,
  [Parameter(Mandatory=$true)][string]$Target,
  [Parameter(Mandatory=$true)][string]$Extensions,
  [switch]$DryRun,
  [ValidateSet("timestamp","increment")][string]$RenameStrategy = "timestamp",
  [string]$LogFile
)

function Ensure-Directory { param([string]$p) if (-not (Test-Path -LiteralPath $p)) { New-Item -ItemType Directory -Path $p -Force | Out-Null } }
function Get-UniqueDestination {
  param($folder,$base,$ext,$strategy)
  $candidate = Join-Path $folder ($base + $ext)
  if (-not (Test-Path -LiteralPath $candidate)) { return $candidate }
  if ($strategy -eq 'timestamp') {
    $ts = (Get-Date).ToString('yyyyMMdd_HHmmssfff')
    return Join-Path $folder ("{0}_{1}{2}" -f $base,$ts,$ext)
  } else {
    $i = 1
    do {
      $candidate = Join-Path $folder ("{0}_{1}{2}" -f $base,$i,$ext)
      $i++
    } while (Test-Path -LiteralPath $candidate)
    return $candidate
  }
}

# --- normalizza Extensions in array robusto ---
if ($null -eq $Extensions) { throw "Devi specificare -Extensions" }
# conserva input grezzo per debug
$rawExtInput = $Extensions

# forza la trasformazione in array di stringhe: split + trim + rimozione del punto + lowercase
[string[]]$Extensions = ($rawExtInput -split '[,; ]+' |
    ForEach-Object { $_.Trim().TrimStart('.') } |
    Where-Object { $_ -ne "" } |
    ForEach-Object { $_.ToLower() })

# debug dettagliato in console
Write-Host "DEBUG: raw Extensions input  = '$rawExtInput'"
Write-Host "DEBUG: raw type              = $($rawExtInput.GetType().Name)"
Write-Host "DEBUG: normalized type       = $($Extensions.GetType().Name)"
Write-Host "DEBUG: normalized count      = $($Extensions.Count)"
Write-Host "DEBUG: normalized values:"
foreach ($e in $Extensions) { Write-Host "  - '$e' (type: $($e.GetType().Name))" }

# diagnostica rapida: mostra tipo e contenuto di $Extensions
Write-Host "DEBUG: Extensions type = $($Extensions.GetType().Name); values = $($Extensions -join ',')"

if (-not $LogFile) { $LogFile = Join-Path $Target "move-by-ext-log.csv" }
Ensure-Directory -p $Target
if (-not (Test-Path -LiteralPath $LogFile)) { "Timestamp,Source,Destination,Status,Message,SizeBytes,LastWriteTime" | Out-File -FilePath $LogFile -Encoding UTF8 }

Write-Host "Root: $Root"
Write-Host "Target: $Target"
Write-Host "Extensions: $($Extensions -join ',')"
if ($DryRun) { Write-Host "DRY-RUN: nessun file verrà spostato." }

$allFiles = Get-ChildItem -Path $Root -Recurse -File -ErrorAction Stop
$matched = $allFiles | Where-Object { ($_.Extension.TrimStart('.').ToLower()) -in $Extensions }

Write-Host "Totale file trovati: $($allFiles.Count). File corrispondenti alle estensioni: $($matched.Count)."
if ($matched.Count -eq 0) { Write-Host "Nessun file corrispondente trovato. Controlla Root ed estensioni."; exit 0 }

foreach ($f in $matched) {
  $sub = Join-Path $Target ($f.Extension.TrimStart('.').ToLower())
  Ensure-Directory -p $sub
  $dest = Get-UniqueDestination -folder $sub -base $f.BaseName -ext $f.Extension -strategy $RenameStrategy
  $ts = (Get-Date).ToString('yyyy-MM-dd HH:mm:ss')
  if ($DryRun) {
    $line = "{0},{1},{2},{3},{4},{5},{6}" -f $ts,('"'+$f.FullName+'"'),('"'+$dest+'"'),"DRY","Simulazione",$f.Length,$f.LastWriteTime.ToString('yyyy-MM-dd HH:mm:ss')
    Add-Content -Path $LogFile -Value $line
    Write-Host "DRY: $($f.FullName) -> $dest"
    continue
  }
  try {
    Move-Item -LiteralPath $f.FullName -Destination $dest -ErrorAction Stop
    $line = "{0},{1},{2},{3},{4},{5},{6}" -f $ts,('"'+$f.FullName+'"'),('"'+$dest+'"'),"OK","Moved",$f.Length,$f.LastWriteTime.ToString('yyyy-MM-dd HH:mm:ss')
    Add-Content -Path $LogFile -Value $line
    Write-Host "OK: $($f.FullName) -> $dest"
  } catch {
    $msg = $_.Exception.Message -replace '[\r\n]+',' '
    $line = "{0},{1},{2},{3},{4},{5},{6}" -f $ts,('"'+$f.FullName+'"'),('"'+$dest+'"'),"ERROR",('"'+($msg -replace '"','""')+'"'),$f.Length,$f.LastWriteTime.ToString('yyyy-MM-dd HH:mm:ss')
    Add-Content -Path $LogFile -Value $line
    Write-Warning "Errore spostamento: $msg"
  }
}
Write-Host "Operazione completata. Log: $LogFile"
