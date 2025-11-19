#!/bin/bash
# Vercel Pre-Deployment Script
# Run this before deploying to verify everything is ready

echo "🔍 Vercel Deployment Pre-Check"
echo "================================"

# Check for required files
echo ""
echo "📋 Checking required files..."

files_to_check=(
    "vercel.json"
    ".vercelignore"
    ".env.example"
    "PHP/db.php"
    "VERCEL_DEPLOYMENT_GUIDE.md"
)

missing_files=0
for file in "${files_to_check[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file"
    else
        echo "❌ Missing: $file"
        missing_files=$((missing_files + 1))
    fi
done

echo ""
echo "🔧 Checking PHP files..."

# Check for critical PHP files
php_files=(
    "PHP/db.php"
    "PHP/home.php"
    "PHP/login/back/sign-up-back.php"
    "Admin/Bookings/back/bookings_backend.php"
)

missing_php=0
for file in "${php_files[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file"
    else
        echo "❌ Missing: $file"
        missing_php=$((missing_php + 1))
    fi
done

echo ""
echo "📝 Verification Summary"
echo "======================"
echo "Missing files: $missing_files"
echo "Missing PHP: $missing_php"

if [ $missing_files -eq 0 ] && [ $missing_php -eq 0 ]; then
    echo ""
    echo "✨ All checks passed! Ready for deployment."
    echo ""
    echo "Next steps:"
    echo "1. Create .env file with your production credentials"
    echo "2. Commit changes: git add . && git commit -m 'Prepare for Vercel'"
    echo "3. Push to GitHub: git push origin your-branch"
    echo "4. Deploy on Vercel dashboard"
    exit 0
else
    echo ""
    echo "⚠️  Please fix missing files before deploying"
    exit 1
fi
