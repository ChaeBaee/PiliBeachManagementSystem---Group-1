# Hosted Database Options for Vercel

## Quick Comparison

| Provider | Free Tier | Setup Time | Best For | Trial |
|----------|-----------|-----------|----------|-------|
| **PlanetScale** | ✅ Yes (5GB) | 2 min | Most projects | Yes |
| **Render.com** | ✅ Yes | 3 min | Simple projects | Yes |
| **AWS RDS** | ✅ 12 months free | 5 min | High scale | Yes |
| **DigitalOcean** | ❌ No | 5 min | Managed simplicity | $200 credit |
| **Heroku Postgres** | ❌ No | 2 min | PaaS approach | Yes |

---

## 🎯 Recommended: PlanetScale

**Why:** Free tier, MySQL compatible, instant setup, perfect for Vercel

### Setup (2 minutes)

1. **Go to:** https://planetscale.com
2. **Sign up** (free account)
3. **Create database:**
   ```
   Click "New database" → Name it "pili_db" → Create
   ```
4. **Get credentials:**
   - Go to "Passwords" tab
   - Click "New password"
   - Select "Connect with...": "MySQL"
   - Copy the connection details:
     ```
     Host: ps_[xxxx].psscale.io
     Username: [your_username]
     Password: [your_password]
     ```
5. **Import database schema:**
   ```sql
   mysql -h ps_[xxxx].psscale.io -u [username] -p pili_db < pili_db.sql
   ```

### Add to Vercel Environment Variables
```
DB_HOST=ps_[xxxx].psscale.io
DB_USER=[your_username]
DB_PASSWORD=[your_password]
DB_NAME=pili_db
DB_PORT=3306
```

### Pros ✅
- Free tier with 5GB storage
- MySQL 8.0 compatible
- Instant setup
- Automatic backups
- Great for Vercel
- Dashboard included

### Cons ⚠️
- Limited to 5GB free tier
- No unlimited connections

**Best for:** Most projects, starting out, good scale

---

## 🚀 AWS RDS

**Why:** Most powerful, scalable, 12-month free tier

### Setup (5 minutes)

1. **Go to:** https://aws.amazon.com/rds/
2. **Create database:**
   - Engine: MySQL 8.0
   - Template: Free tier
   - DB instance identifier: `pili-db`
   - Master username: `admin`
   - Auto-generate password
3. **Set connectivity:**
   - Public accessibility: YES
   - New security group: `vercel-access`
4. **Modify security group:**
   - Allow inbound: MySQL/3306 from anywhere (0.0.0.0/0)
5. **Get connection details:**
   - Endpoint: `pili-db.xxxxx.rds.amazonaws.com`
   - Port: `3306`

### Add to Vercel Environment Variables
```
DB_HOST=pili-db.xxxxx.rds.amazonaws.com
DB_USER=admin
DB_PASSWORD=[generated_password]
DB_NAME=pili_db
DB_PORT=3306
```

### Pros ✅
- 12-month free tier (t2.micro)
- Highly scalable
- Automatic backups
- Multi-AZ available
- Managed service
- Great for production

### Cons ⚠️
- More complex setup
- Can be expensive after free tier
- Network latency if far from region

**Best for:** Production-grade applications, long term

---

## 💻 DigitalOcean Managed Databases

**Why:** Simple, managed, good pricing

### Setup (5 minutes)

1. **Go to:** https://www.digitalocean.com/
2. **Create managed database:**
   - Database engine: MySQL 8.0
   - Datacenter region: Close to users
   - Plan: Standard (starts at $15/month)
3. **Configure:**
   - Cluster name: `pili-db`
   - Enable connection pool: Yes
4. **Get credentials:**
   - Host: `db-mysql-xxx.ondigitalocean.com`
   - User: `doadmin`
   - Password: [automatically generated]

### Add to Vercel Environment Variables
```
DB_HOST=db-mysql-xxx.ondigitalocean.com
DB_USER=doadmin
DB_PASSWORD=[generated_password]
DB_NAME=pili_db
DB_PORT=3306
```

### Pros ✅
- Simple to use
- Connection pooling included
- Managed backups
- Good pricing
- Simple pricing model
- $200 startup credit available

### Cons ⚠️
- No free tier
- Minimum ~$15/month

**Best for:** Growing projects, predictable costs

---

## 🔄 Render.com MySQL

**Why:** Simple, fast setup, free tier

### Setup (3 minutes)

1. **Go to:** https://render.com
2. **Create database:**
   - MySQL 8.0
   - Free tier
3. **Get connection info from dashboard**

### Add to Vercel Environment Variables
```
DB_HOST=[render_host]
DB_USER=[username]
DB_PASSWORD=[password]
DB_NAME=pili_db
DB_PORT=3306
```

### Pros ✅
- Free tier available
- Very simple setup
- Auto-sleep on free tier

### Cons ⚠️
- Limited connections
- May auto-sleep

**Best for:** Development, testing

---

## 📊 Decision Matrix

### Question 1: What's your budget?
- **$0** → PlanetScale or Render
- **$0-50/month** → DigitalOcean
- **Willing to scale** → AWS

### Question 2: When do you need it?
- **ASAP** → PlanetScale (2 min)
- **Today** → AWS (30 min)
- **This week** → Any option

### Question 3: What scale?
- **Small project** → PlanetScale free tier
- **Medium project** → DigitalOcean
- **Large/growing** → AWS RDS

---

## 🔧 Database Migration Steps

For all providers:

1. **Export current database:**
   ```bash
   mysqldump -h localhost -u root pili_db > backup.sql
   ```

2. **Connect to new database:**
   ```bash
   mysql -h [new_host] -u [user] -p -e "CREATE DATABASE pili_db"
   ```

3. **Import schema:**
   ```bash
   mysql -h [new_host] -u [user] -p pili_db < backup.sql
   ```

4. **Test connection:**
   ```bash
   mysql -h [new_host] -u [user] -p -e "SELECT 1"
   ```

---

## ✅ Pre-Deployment Checklist

- [ ] Database created and accessible
- [ ] All credentials copied
- [ ] Schema imported successfully
- [ ] Test connection works
- [ ] 5 environment variables ready for Vercel
- [ ] Backup of local database taken

---

## 🚨 Troubleshooting Database Connection

### "Can't connect to database"
1. ✅ Check host/username/password in env vars
2. ✅ Verify database is accessible from internet
3. ✅ Add Vercel IP to database firewall (if required)
4. ✅ Check database is actually running

### "Connection timeout"
1. ✅ Verify DB_HOST is correct (no typos)
2. ✅ Check network connectivity
3. ✅ Increase timeout in db.php if needed

### "Wrong password"
1. ✅ Re-copy password (beware special chars)
2. ✅ Check environment variables in Vercel
3. ✅ Verify it's not cached (redeploy)

---

## 📞 Support Links

- **PlanetScale Docs:** https://docs.planetscale.com
- **AWS RDS Docs:** https://docs.aws.amazon.com/rds/
- **DigitalOcean Docs:** https://docs.digitalocean.com/products/databases/
- **Render Docs:** https://render.com/docs/databases

---

## 💡 Pro Tips

1. **Backup regularly** - Set up automated backups in your DB provider
2. **Monitor connections** - Watch connection pool usage
3. **Add indexes** - Create indexes on frequently searched columns
4. **Use connection pooling** - Most providers offer this
5. **Test failover** - Know how to restore from backup

---

**Recommendation:** Start with **PlanetScale** for free tier, switch to **AWS RDS** or **DigitalOcean** when scaling.
