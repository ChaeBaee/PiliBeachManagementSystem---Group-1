# Vercel Configuration Checklist

## Before Deploying

- [ ] All code committed to GitHub
- [ ] `.env.example` created with template variables
- [ ] `vercel.json` configured
- [ ] `.vercelignore` set up
- [ ] `db.php` updated to use environment variables
- [ ] Hosted database created (PlanetScale, AWS RDS, etc.)
- [ ] Database credentials obtained and verified

## Vercel Project Setup

- [ ] GitHub repository connected to Vercel
- [ ] Project created on Vercel dashboard
- [ ] Build settings configured
- [ ] Environment variables added:
  - [ ] `DB_HOST`
  - [ ] `DB_USER`
  - [ ] `DB_PASSWORD`
  - [ ] `DB_NAME`
  - [ ] `DB_PORT`
  - [ ] `SMTP_HOST` (if using email)
  - [ ] `SMTP_USER` (if using email)
  - [ ] `SMTP_PASSWORD` (if using email)

## After Deployment

- [ ] Access Vercel domain and verify it loads
- [ ] Test login/registration functionality
- [ ] Test database queries (view bookings, rooms, etc.)
- [ ] Check browser console for errors
- [ ] Check Vercel logs for issues
- [ ] Test file uploads (if applicable)
- [ ] Verify email functionality (if applicable)
- [ ] Check session management

## Production Verification

- [ ] Database backups configured
- [ ] Error logging enabled
- [ ] Performance monitoring set up
- [ ] Custom domain configured (if applicable)
- [ ] SSL certificate verified (automatic with Vercel)
- [ ] Monitoring alerts configured

## Troubleshooting Checklist

If deployment fails:
1. [ ] Check Vercel deployment logs
2. [ ] Verify all environment variables are set
3. [ ] Test database connection string locally
4. [ ] Check file paths are correct
5. [ ] Ensure uploaded database schema is complete
6. [ ] Verify PHP version compatibility (8.2+)
7. [ ] Check for hardcoded localhost references

## Database Migration Checklist

Before using production database:
1. [ ] Create database on hosting provider
2. [ ] Run SQL schema files
3. [ ] Test connection with credentials
4. [ ] Verify all tables created successfully
5. [ ] Add sample data if needed
6. [ ] Backup configuration saved

## Security Checklist

- [ ] Never commit `.env` file with real credentials
- [ ] Use strong database passwords
- [ ] Enable database firewall rules
- [ ] Restrict database access to Vercel only (if possible)
- [ ] Regular backups configured
- [ ] API keys stored in Vercel environment variables only
