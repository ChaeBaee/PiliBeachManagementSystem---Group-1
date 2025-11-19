# Vercel Deployment Pre-Check for Windows PowerShell
# Run: .\check-deployment.ps1

Write-Host "🔍 Vercel Deployment Pre-Check" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

# Check for required files
Write-Host "📋 Checking required files..." -ForegroundColor Yellow

$files_to_check = @(
    "vercel.json",
    ".vercelignore",
    ".env.example",
    "PHP/db.php",
    "VERCEL_DEPLOYMENT_GUIDE.md"
)

$missing_files = 0
foreach ($file in $files_to_check) {
    if (Test-Path $file) {
        Write-Host "✅ $file" -ForegroundColor Green
    } else {
        Write-Host "❌ Missing: $file" -ForegroundColor Red
        $missing_files++
    }
}

Write-Host ""
Write-Host "🔧 Checking critical PHP files..." -ForegroundColor Yellow

$php_files = @(
    "PHP/db.php",
    "PHP/home.php",
    "Admin/Dashboard/front/index.php"
)

$missing_php = 0
foreach ($file in $php_files) {
    if (Test-Path $file) {
        Write-Host "✅ $file" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Optional: $file" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "📝 Verification Summary" -ForegroundColor Cyan
Write-Host "======================" -ForegroundColor Cyan
Write-Host "Missing required files: $missing_files"

if ($missing_files -eq 0) {
    Write-Host ""
    Write-Host "✨ All checks passed! Ready for deployment." -ForegroundColor Green
    Write-Host ""
    Write-Host "Next steps:" -ForegroundColor Yellow
    Write-Host "1. Create .env file with your production credentials"
    Write-Host "2. Commit changes: git add . && git commit -m 'Prepare for Vercel'"
    Write-Host "3. Push to GitHub: git push origin your-branch"
    Write-Host "4. Deploy on Vercel dashboard at https://vercel.com/dashboard"
    Write-Host ""
    Write-Host "For more info, see: VERCEL_DEPLOYMENT_GUIDE.md" -ForegroundColor Cyan
    exit 0
} else {
    Write-Host ""
    Write-Host "⚠️  Please fix missing files before deploying" -ForegroundColor Red
    exit 1
}
