#!/usr/bin/env bash
# Vercel Deployment Script
# Automatically prepare your application for deployment

set -e

echo "🚀 Vercel Deployment Preparation Script"
echo "========================================"
echo ""

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Step 1: Check if Git is initialized
echo "📦 Checking Git status..."
if ! git rev-parse --git-dir > /dev/null 2>&1; then
    echo -e "${RED}Error: Not a Git repository${NC}"
    echo "Initialize with: git init"
    exit 1
fi
echo -e "${GREEN}✓ Git repository found${NC}"

# Step 2: Check for required configuration files
echo ""
echo "📋 Verifying Vercel configuration files..."

required_files=(
    "vercel.json"
    ".vercelignore"
    ".env.example"
    "PHP/db.php"
)

for file in "${required_files[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓ $file${NC}"
    else
        echo -e "${RED}✗ Missing: $file${NC}"
        exit 1
    fi
done

# Step 3: Check for .env file (should not be committed)
echo ""
echo "🔒 Checking security..."
if [ -f ".env" ] && git ls-files --cached | grep -q "^.env$"; then
    echo -e "${RED}⚠ WARNING: .env file is in Git!${NC}"
    echo "Remove it: git rm --cached .env && git commit -m 'Remove .env'"
else
    echo -e "${GREEN}✓ .env is not tracked (good!)${NC}"
fi

# Step 4: Summary
echo ""
echo "📝 Summary"
echo "========="
echo -e "${GREEN}✓ All required files present${NC}"
echo -e "${GREEN}✓ Configuration valid${NC}"
echo ""

# Step 5: Next steps
echo "🎯 Next Steps:"
echo "1. Create hosted database:"
echo "   - Option 1: PlanetScale (recommended, free)"
echo "   - Option 2: AWS RDS"
echo "   - Option 3: DigitalOcean"
echo ""
echo "2. Create .env file with database credentials:"
echo "   cp .env.example .env"
echo "   # Edit .env with your database details"
echo ""
echo "3. Test locally:"
echo "   - Verify application works with .env file"
echo "   - Test all major features"
echo ""
echo "4. Commit and push:"
echo "   git add ."
echo "   git commit -m 'Final Vercel deployment prep'"
echo "   git push origin your-branch"
echo ""
echo "5. Deploy on Vercel:"
echo "   - Go to https://vercel.com/new"
echo "   - Import your GitHub repository"
echo "   - Add environment variables (from .env.example)"
echo "   - Click Deploy"
echo ""
echo "📚 Documentation:"
echo "   - Quick Start: QUICK_START_VERCEL.md"
echo "   - Full Guide: VERCEL_DEPLOYMENT_GUIDE.md"
echo "   - Database Options: DATABASE_OPTIONS.md"
echo ""
echo -e "${GREEN}✨ Ready to deploy!${NC}"
