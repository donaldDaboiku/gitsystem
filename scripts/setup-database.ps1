# Import database into local MySQL (Laragon)
$root = Split-Path -Parent $PSScriptRoot
$sqlFile = Join-Path $root "backend\database.sql"

Write-Host "Importing gitsystemng database..." -ForegroundColor Cyan
Write-Host "You may be prompted for your MySQL root password (Laragon: often blank — just press Enter)." -ForegroundColor DarkGray

mysql -u root -p -e "SOURCE $sqlFile"

if ($LASTEXITCODE -eq 0) {
    Write-Host "Database imported successfully." -ForegroundColor Green
} else {
    Write-Host "Import failed. Open Laragon, start MySQL, then run:" -ForegroundColor Red
    Write-Host "  mysql -u root -p < backend\database.sql"
}
