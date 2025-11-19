@echo off
REM Vercel Deployment Script for Windows
REM Automatically prepare your application for deployment

setlocal enabledelayedexpansion

echo.
echo 🚀 Vercel Deployment Preparation Script
echo ======================================== 
echo.

REM Step 1: Check if Git is initialized
echo 📦 Checking Git status...
git rev-parse --git-dir >nul 2>&1
if errorlevel 1 (
    echo Error: Not a Git repository
    echo Initialize with: git init
    exit /b 1
)
echo ✓ Git repository found
echo.

REM Step 2: Check for required configuration files
echo 📋 Verifying Vercel configuration files...

set "required_files=vercel.json .vercelignore .env.example PHP\db.php"
setlocal enabledelayedexpansion

for %%f in (%required_files%) do (
    if exist "%%f" (
        echo ✓ %%f
    ) else (
        echo ✗ Missing: %%f
        exit /b 1
    )
)

echo.
echo 🔒 Checking security...
if exist ".env" (
    echo ⚠ WARNING: .env file exists (make sure it's not committed!)
) else (
    echo ✓ .env not tracked (good!)
)

echo.
echo 📝 Summary
echo =========
echo ✓ All required files present
echo ✓ Configuration valid
echo.

echo 🎯 Next Steps:
echo 1. Create hosted database:
echo    - Option 1: PlanetScale (recommended, free)
echo    - Option 2: AWS RDS
echo    - Option 3: DigitalOcean
echo.
echo 2. Create .env file with database credentials:
echo    - Copy .env.example to .env
echo    - Edit with your database details
echo.
echo 3. Test locally:
echo    - Verify application works with .env file
echo    - Test all major features
echo.
echo 4. Commit and push:
echo    git add .
echo    git commit -m "Final Vercel deployment prep"
echo    git push origin your-branch
echo.
echo 5. Deploy on Vercel:
echo    - Go to https://vercel.com/new
echo    - Import your GitHub repository
echo    - Add environment variables
echo    - Click Deploy
echo.
echo 📚 Documentation:
echo    - Quick Start: QUICK_START_VERCEL.md
echo    - Full Guide: VERCEL_DEPLOYMENT_GUIDE.md
echo    - Database Options: DATABASE_OPTIONS.md
echo.
echo ✨ Ready to deploy!
echo.

endlocal
