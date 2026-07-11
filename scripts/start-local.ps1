# Start GITSYSTEMNG locally (requires Laragon MySQL running)
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

Write-Host ""
Write-Host "GITSYSTEMNG local server" -ForegroundColor Green
Write-Host "  Laragon: http://gitsystemng.test  (restart Laragon after first link)"
Write-Host "  Site:    http://gitsystemng.test/frontend/index.html"
Write-Host "  Admin:   http://gitsystemng.test/backend/admin/login.php"
Write-Host ""
Write-Host "Login: admin / admin123" -ForegroundColor Yellow
Write-Host "Press Ctrl+C to stop." -ForegroundColor DarkGray
Write-Host ""

php -S localhost:8000 -t $root
