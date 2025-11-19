# Vercel Deployment Guide - Pili Beach Management System

## Overview
This guide explains how to deploy the Pili Beach Management System to Vercel with proper database configuration and environment variable management.

## Prerequisites
- GitHub account with the repository
- Vercel account (free tier available)
- MySQL/MariaDB database (hosted on services like PlanetScale, AWS RDS, or DigitalOcean)
- GitHub CLI or Git installed

## Step 1: Prepare Your Repository

1. **Ensure all files are committed to Git:**
   ```bash
   git add .
   git commit -m "Prepare for Vercel deployment"
   git push origin your-branch-name
   ```

2. **Verify the following files exist in your project root:**
   - `vercel.json` - Vercel configuration
   - `.vercelignore` - Files to exclude from deployment
   - `.env.example` - Environment variables template
   - `PHP/db.php` - Updated database connection with env support

## Step 2: Set Up Hosted Database

For Vercel deployment, you need a publicly accessible database. Choose one:

### Option A: PlanetScale (Recommended - Free tier available)
1. Go to https://planetscale.com
2. Create a free account
3. Create a new database
4. Get connection credentials from the "Passwords" tab
5. Keep these credentials safe for Vercel setup

### Option B: AWS RDS
1. Create RDS instance with MySQL 8.0+
2. Configure security groups to allow external connections
3. Note the endpoint, username, password, and database name

### Option C: DigitalOcean Managed Databases
1. Create managed MySQL cluster
2. Get connection details from dashboard
3. Add your Vercel IP to allowed hosts (or use % for any host)

## Step 3: Deploy to Vercel

1. **Go to Vercel Dashboard:** https://vercel.com/dashboard

2. **Connect GitHub Repository:**
   - Click "New Project"
   - Select "Import Git Repository"
   - Choose your GitHub repository
   - Click "Import"

3. **Configure Environment Variables:**
   - In the project settings, go to "Environment Variables"
   - Add the following variables:
     ```
     DB_HOST=your-database-host.com
     DB_USER=your-database-user
     DB_PASSWORD=your-database-password
     DB_NAME=your-database-name
     DB_PORT=3306
     SMTP_HOST=smtp.gmail.com (if using Gmail)
     SMTP_USER=your-email@gmail.com (if using email features)
     SMTP_PASSWORD=your-app-password (Gmail App Password)
     ```

4. **Configure Build Settings:**
   - Build Command: Leave default or use `echo 'PHP application'`
   - Output Directory: Leave empty (root directory)
   - Install Command: Leave default

5. **Deploy:**
   - Click "Deploy"
   - Wait for deployment to complete
   - You'll get a .vercel.app domain

## Step 4: Verify Deployment

1. **Check Deployment Status:**
   - Visit your Vercel project dashboard
   - Check deployment logs if there are issues

2. **Test Application:**
   - Visit your Vercel domain
   - Test login functionality
   - Test database queries
   - Check browser console and server logs for errors

## Step 5: Configure Custom Domain (Optional)

1. In Vercel Project Settings → Domains
2. Add your custom domain (e.g., example.com)
3. Update DNS records as instructed by Vercel
4. Wait for DNS propagation (5-30 minutes)

## Troubleshooting

### Database Connection Failed
- **Cause:** Environment variables not set correctly
- **Solution:** 
  1. Check Vercel Environment Variables are correct
  2. Verify database host is publicly accessible
  3. Check database credentials
  4. Add Vercel IP to database firewall if needed

### 502 Bad Gateway Error
- **Cause:** PHP error or database issue
- **Solution:**
  1. Check Vercel deployment logs
  2. Verify DB_HOST, DB_USER, DB_PASSWORD are set
  3. Test database connection manually

### File Upload Not Working
- **Cause:** /tmp directory permissions
- **Solution:**
  1. Create `uploads` directory in public root
  2. Ensure file paths are relative
  3. Use proper chmod settings

### Session Issues
- **Cause:** Stateless serverless environment
- **Solution:**
  1. Consider using database-backed sessions
  2. Or use Redis (Upstash free tier)
  3. Or use JWT tokens for API calls

## Advanced Configuration

### Using Redis for Sessions (Recommended)

1. **Sign up for Upstash:** https://upstash.com (free tier)

2. **Get Redis URL from Upstash dashboard**

3. **Add to Vercel Environment Variables:**
   ```
   REDIS_URL=redis://...
   ```

4. **Update session configuration in PHP:**
   ```php
   // In db.php or config file
   if (getenv('REDIS_URL')) {
       ini_set('session.save_handler', 'redis');
       ini_set('session.save_path', getenv('REDIS_URL'));
   }
   ```

### Database Migration

Before deploying with production database:

1. **Backup your current database:**
   ```bash
   mysqldump -h localhost -u root pili_db > backup.sql
   ```

2. **Import to hosted database:**
   ```bash
   mysql -h your-host -u user -p database_name < backup.sql
   ```

3. **Test connection before going live**

## Monitoring and Maintenance

1. **Check Logs:**
   - Vercel Dashboard → Deployments → Logs
   - Look for errors and warnings

2. **Monitor Database:**
   - Check connection limits
   - Monitor query performance
   - Regular backups

3. **Security Best Practices:**
   - Never commit `.env` file
   - Rotate database passwords regularly
   - Use strong passwords for all services
   - Enable HTTPS (Vercel does automatically)

## Rollback to Previous Version

If deployment breaks:

1. In Vercel Dashboard → Deployments
2. Find last working deployment
3. Click the three dots menu
4. Select "Redeploy"

## Local Development

To test locally with the same environment:

1. **Create `.env` file (don't commit):**
   ```
   DB_HOST=localhost
   DB_USER=root
   DB_PASSWORD=
   DB_NAME=pili_db
   DB_PORT=3306
   ```

2. **Run locally with XAMPP:**
   - Start MySQL and Apache in XAMPP
   - Access via http://localhost

3. **Test with production database (optional):**
   - Update `.env` with production credentials
   - Test full functionality before deploying

## CI/CD Pipeline

Vercel automatically deploys when you push to your repository branch:

1. **Push to main branch:**
   ```bash
   git push origin main
   ```

2. **Vercel automatically builds and deploys**

3. **Check deployment at vercel.com/dashboard**

## Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| "Cannot connect to database" | Check DB credentials and network access |
| "File upload fails" | Create uploads folder, check permissions |
| "Session not persisting" | Use database sessions or Redis |
| "Images not loading" | Check file paths are absolute where needed |
| "Slow performance" | Check database queries, add indexes |
| "Email not sending" | Verify SMTP credentials, check logs |

## Support Resources

- Vercel Docs: https://vercel.com/docs
- PHP on Vercel: https://vercel.com/docs/concepts/functions/serverless-functions/runtimes/php
- PlanetScale: https://docs.planetscale.com
- MySQL Best Practices: https://dev.mysql.com/doc/

## Next Steps

1. Set up continuous deployment from GitHub
2. Configure custom domain
3. Set up SSL certificate (automatic with Vercel)
4. Monitor application performance
5. Plan for scaling if needed

---

**Last Updated:** November 2025
