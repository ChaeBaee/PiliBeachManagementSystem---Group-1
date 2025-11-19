# Git Commit Guide for Vercel Deployment

## Ready to Commit? Here's What to Do

### Step 1: Check What's New

```powershell
git status
```

You should see new files for Vercel configuration.

### Step 2: Review Changes

```powershell
git diff PHP/db.php
```

Verify the database connection was updated.

### Step 3: Stage All Changes

```powershell
git add .
```

### Step 4: Commit with Appropriate Message

Choose the commit message that fits your situation:

#### Option A: Simple Commit (Recommended)
```powershell
git commit -m "Configure for Vercel deployment"
```

#### Option B: Detailed Commit
```powershell
git commit -m "Add Vercel deployment configuration

- Add vercel.json for serverless PHP runtime
- Update db.php to support environment variables
- Add .vercelignore to exclude unnecessary files
- Add .env.example template for env variables
- Add comprehensive deployment documentation
- Add health check and API routing endpoints"
```

#### Option C: Feature Branch Commit
```powershell
git commit -m "feat: add Vercel deployment support

BREAKING CHANGE: Database connection now requires environment variables.
For local dev, create .env file with database credentials.

- Adds vercel.json configuration file
- Updates PHP/db.php for env var support
- Creates documentation for deployment
- Maintains backward compatibility with localhost"
```

### Step 5: Push to GitHub

```powershell
git push origin your-branch-name
```

Or if on main:
```powershell
git push origin main
```

### Step 6: Verify on GitHub

1. Go to https://github.com/your-repo
2. Check the latest commit appears
3. Verify files are there
4. Ready for Vercel!

---

## Commit Message Examples by Scenario

### Scenario 1: First Time Deploying
```
git commit -m "Add Vercel deployment configuration"
```

### Scenario 2: Adding to Existing Deployment
```
git commit -m "Update deployment configuration for prod database"
```

### Scenario 3: Bug Fix for Deployment
```
git commit -m "Fix database connection for Vercel deployment"
```

### Scenario 4: Optimization
```
git commit -m "Optimize configuration for Vercel deployment"
```

---

## What NOT to Commit

Make sure these are in `.vercelignore` or `.gitignore`:

❌ `.env` (local secrets)
❌ `vendor/` (PHP dependencies)
❌ `node_modules/` (JavaScript deps)
❌ `.git/` (Git repository)
❌ `*.sql` (database backups)
❌ `test_*.php` (test files)
❌ `.DS_Store` (Mac files)
❌ `Thumbs.db` (Windows files)

---

## After Committing

### Push to GitHub
```powershell
git push origin your-branch
```

### Connect to Vercel
1. Go to https://vercel.com/new
2. Select your GitHub repo
3. Add environment variables
4. Deploy!

### Verify Deployment
1. Check Vercel dashboard
2. Wait for deployment to complete
3. Visit your-app.vercel.app
4. Test functionality

---

## Useful Git Commands

### Check current branch
```powershell
git branch
```

### Switch to main
```powershell
git checkout main
```

### See all changes
```powershell
git diff
```

### See staged changes
```powershell
git diff --staged
```

### Undo changes to a file
```powershell
git checkout -- filename.php
```

### View commit history
```powershell
git log --oneline -10
```

### Create a new branch for deployment
```powershell
git checkout -b vercel-deployment
```

---

## Troubleshooting

### "fatal: not a git repository"
```powershell
# Initialize git if needed
git init
git remote add origin https://github.com/your-repo
git fetch origin
git checkout -b your-branch
```

### "Your branch is behind"
```powershell
# Pull latest changes
git pull origin your-branch
```

### "Changes not staged"
```powershell
# Stage all changes
git add .
```

### "Nothing to commit"
```powershell
# Files are already committed
# Make changes to existing files first
```

---

## Best Practices

✅ **DO:**
- Write clear commit messages
- Commit related changes together
- Push frequently
- Check status before committing
- Use descriptive branch names

❌ **DON'T:**
- Commit .env files
- Commit large binary files
- Commit without testing locally
- Commit commented-out code (unless necessary)
- Commit with vague messages like "update" or "fix"

---

## After Successful Deployment

Create a tag to mark the deployment:

```powershell
git tag -a v1.0-vercel -m "First Vercel deployment"
git push origin v1.0-vercel
```

---

## For Future Deployments

Every git push will automatically deploy to Vercel:

```powershell
# Make changes
git add .
git commit -m "Your changes"
git push origin main

# Vercel automatically deploys!
```

---

**You're ready to commit and deploy!**

Next: Follow the deployment steps in QUICK_START_VERCEL.md
