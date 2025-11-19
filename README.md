WebSystem and Database Project
Members:
Lunaspe
Ocuaman
Ronatay
Janobas
Caburog

---

## 🚀 Vercel Deployment Ready

This project is now configured for deployment on Vercel with cloud database support.

### Quick Start
1. **Read:** `QUICK_START_VERCEL.md` (5 minutes)
2. **Choose database:** See `DATABASE_OPTIONS.md`
3. **Deploy:** Follow deployment steps in Vercel dashboard

### Documentation
- 📖 `QUICK_START_VERCEL.md` - Quick deployment guide
- 📋 `VERCEL_DEPLOYMENT_GUIDE.md` - Detailed setup
- ✅ `DEPLOYMENT_CHECKLIST.md` - Pre/post deployment checks
- 💾 `DATABASE_OPTIONS.md` - Hosted database comparison
- 📊 `DEPLOYMENT_SUMMARY.md` - What was changed

### Vercel Configuration Files
- `vercel.json` - Vercel deployment config
- `.vercelignore` - Files to exclude
- `.env.example` - Environment variables template
- `PHP/db.php` - Updated for env var support

### For Development
- Local: Use XAMPP with localhost
- Production: Use Vercel with cloud database
- Both use same `db.php` (env var fallback)

### Key Features
✅ Environment variable support  
✅ Local dev & cloud production compatible  
✅ Automatic deployment on git push  
✅ Zero downtime deployments  
✅ Global CDN  
✅ Auto SSL/HTTPS  

### Database Connection
- Reads from environment variables
- Fallback to localhost (development)
- Supports any MySQL-compatible database
- Recommended: PlanetScale (free tier)

---
