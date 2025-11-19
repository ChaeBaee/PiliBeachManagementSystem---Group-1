# Vercel Deployment - Troubleshooting Guide

## 🆘 Common Issues & Solutions

### Issue 1: "Database connection failed" Error

**What you see:** 
```
Database connection failed. Please try again later.
```

**Most likely cause:** Environment variables not set correctly

**How to fix:**
1. Go to Vercel Dashboard
2. Click your project → Settings → Environment Variables
3. Verify all 5 variables are set:
   - `DB_HOST` ✓ (not `localhost`)
   - `DB_USER` ✓
   - `DB_PASSWORD` ✓
   - `DB_NAME` ✓
   - `DB_PORT` ✓
4. Check for typos or extra spaces
5. Redeploy: Deployments → Redeploy

**Double-check:**
```
❌ DB_HOST = "localhost"      (wrong for Vercel)
✅ DB_HOST = "db.example.com" (correct)

❌ DB_PASSWORD = "my pass"    (spaces inside)
✅ DB_PASSWORD = "my_pass"    (no spaces or quoted)
```

---

### Issue 2: "502 Bad Gateway" Error

**What you see:**
```
502 - Bad Gateway
```

**Possible causes:**
1. Database credentials wrong
2. Database not running
3. PHP error in code
4. Database host not accessible

**How to fix:**

**Step 1:** Check Vercel logs
```
1. Dashboard → Your project
2. Deployments → Latest deployment
3. Scroll to "Logs" section
4. Look for error messages
```

**Step 2:** Verify database is running
```bash
# Test database connection locally first
mysql -h [your_host] -u [user] -p [password] [database]
```

**Step 3:** Check environment variables
```
Go to Vercel Settings → Environment Variables
Verify all values match your database
```

**Step 4:** If still failing
- Try redeploying
- Check database firewall allows external connections
- Verify database is publicly accessible

---

### Issue 3: "File uploads not working"

**What you see:**
- Upload button doesn't work
- Files not saved
- 500 error on upload

**How to fix:**

1. **Create uploads directory:**
   ```bash
   mkdir -p PHP/uploads
   ```

2. **Make it writable:**
   ```bash
   chmod 755 PHP/uploads
   ```

3. **Use absolute paths in code:**
   ```php
   // Bad
   $filepath = "uploads/file.jpg";
   
   // Good
   $filepath = "/tmp/uploads/file.jpg";
   // or
   $filepath = __DIR__ . "/uploads/file.jpg";
   ```

4. **Redeploy and test**

---

### Issue 4: "Login/Session not working"

**What you see:**
- Can login but logged out on next page
- Session lost on page refresh
- Can't stay logged in

**Why it happens:**
Vercel is serverless - each request may go to different instance

**How to fix:**

**Option 1: Use Database Sessions (Recommended)**
```php
// In PHP/db.php, after connection:
ini_set('session.save_handler', 'user');
session_set_save_handler(
    function ($path, $name) { return true; },
    function () { return true; },
    function ($id) { /* read from DB */ },
    function ($id, $data) { /* write to DB */ },
    function ($id) { /* delete from DB */ },
    function ($maxlifetime) { /* cleanup */ }
);
```

**Option 2: Use JWT Tokens**
```php
// Instead of sessions, use JWT in headers
$token = jwt_encode(['user_id' => $user_id]);
// Send token to frontend, store in localStorage
// Send in Authorization header on requests
```

**Option 3: Use Redis (Free tier available)**
```php
// Install Upstash Redis (free)
ini_set('session.save_handler', 'redis');
ini_set('session.save_path', getenv('REDIS_URL'));
```

---

### Issue 5: "Images not loading" or "404 on assets"

**What you see:**
- Broken image icons
- CSS/JS files not loading
- 404 errors in console

**How to fix:**

1. **Check file paths:**
   ```html
   <!-- Bad (relative) -->
   <img src="assets/image.jpg">
   
   <!-- Good (absolute from domain) -->
   <img src="/assets/image.jpg">
   ```

2. **Move assets to public directory:**
   ```
   Create: PHP/assets/
   Move all images there
   ```

3. **Update paths in HTML/CSS:**
   ```php
   // In PHP files
   <img src="<?php echo $_ENV['API_URL']; ?>/assets/image.jpg">
   ```

4. **Verify files are included in deployment**
   - Check `.vercelignore` doesn't exclude them
   - Make sure `assets/` folder exists
   - Redeploy and check

---

### Issue 6: "Email not sending"

**What you see:**
- Contact form submission shows success but no email
- No error messages

**How to fix:**

1. **Verify SMTP credentials in Vercel env vars:**
   - `SMTP_HOST` = smtp.gmail.com
   - `SMTP_USER` = your-email@gmail.com
   - `SMTP_PASSWORD` = your-app-password (not regular password)
   - `SMTP_PORT` = 587 or 465

2. **If using Gmail:**
   - Enable 2-Factor Authentication
   - Create App Password (not account password)
   - Use that in `SMTP_PASSWORD`

3. **Test locally first:**
   ```bash
   # Add SMTP to .env and test locally
   php -r "require 'PHP/contact/back/contact_backend.php';"
   ```

4. **Check Vercel logs for errors:**
   - Go to deployment logs
   - Search for "mail" or "SMTP"
   - Look for error messages

---

### Issue 7: "Database timeout/connection refused"

**What you see:**
```
SQLSTATE[HY000]: General error: 2003 Can't connect to MySQL server
```

**Causes:**
1. Database host not accessible from internet
2. Database firewall blocking Vercel IPs
3. Database credentials wrong
4. Database isn't running

**How to fix:**

1. **Check database accessibility:**
   ```bash
   # Try connecting locally first
   mysql -h [host] -u [user] -p [password]
   ```

2. **Verify database firewall:**
   - If using PlanetScale: Should work automatically
   - If using AWS RDS: Add security group rule
   - If using DigitalOcean: Check trusted sources
   - If using other: Look for firewall/ACL settings

3. **Check credentials (again):**
   - Copy directly from database provider
   - No extra spaces
   - Special characters properly escaped
   - Verify in Vercel environment variables

4. **Add debug logging:**
   ```php
   // In PHP/db.php
   error_log("Connecting to: " . $servername);
   error_log("User: " . $username);
   error_log("Database: " . $dbname);
   ```

---

### Issue 8: "Maximum function size exceeded"

**What you see:**
```
Maximum function size exceeded
```

**Cause:** A single PHP file is too large for Vercel

**How to fix:**
- Break large PHP files into smaller functions
- Move code to separate files
- Use includes/requires to load modules
- Most common in `bookings_backend.php` or `users_backend.php`

---

### Issue 9: "Module/Extension not available"

**What you see:**
```
Call to undefined function curl_init()
```

**Cause:** PHP extension not available in Vercel runtime

**How to fix:**
1. Check what extensions are available
2. Use alternative functions
3. Most common: cURL, GD, PDO (all available)
4. If needed, create workaround in code

---

### Issue 10: "Out of memory" or "Timeout"

**What you see:**
```
fatal error: allowed memory size exceeded
or
504 Gateway Timeout
```

**Cause:** Query too slow or processing too much data

**How to fix:**

1. **Add database indexes:**
   ```sql
   CREATE INDEX idx_user_id ON bookings(user_id);
   CREATE INDEX idx_room_id ON rooms(room_id);
   ```

2. **Optimize queries:**
   ```php
   // Bad - loads all data
   SELECT * FROM large_table;
   
   // Good - specific columns and limit
   SELECT id, name, email FROM users LIMIT 100;
   ```

3. **Add pagination:**
   ```php
   $page = $_GET['page'] ?? 1;
   $limit = 20;
   $offset = ($page - 1) * $limit;
   
   $query = "SELECT * FROM table LIMIT $limit OFFSET $offset";
   ```

4. **Verify database connection timeout:**
   ```php
   // In PHP/db.php
   PDO::ATTR_TIMEOUT => 30,
   ```

---

## 🔍 Debug Checklist

When something doesn't work:

1. **Check Vercel logs:**
   ```
   Dashboard → Project → Deployments → Latest → Logs
   ```

2. **Check browser console:**
   ```
   F12 → Console tab → Look for errors
   ```

3. **Test database locally:**
   ```bash
   mysql -h [host] -u [user] -p [password] [db]
   ```

4. **Verify environment variables:**
   - Vercel Dashboard → Settings → Environment Variables
   - Compare with `.env.example`

5. **Redeploy:**
   - Sometimes fixes issues
   - Clears cache
   - Updates all servers

6. **Check logs:**
   ```
   echo "Debug point reached" | php error_log();
   ```

---

## 🚀 Quick Fixes (Try First)

1. **Redeploy** - Fixes most issues
2. **Clear cache** - Browser F5 + hard refresh (Ctrl+Shift+R)
3. **Check env vars** - Most common mistake
4. **Check database** - Verify it's running and accessible
5. **Check logs** - Always look at Vercel logs first

---

## 📞 Get More Help

1. **Read:** Specific guide in VERCEL_DEPLOYMENT_GUIDE.md
2. **Check:** Vercel documentation https://vercel.com/docs
3. **Search:** Your error message on Stack Overflow
4. **Ask:** In Vercel community forums

---

## 📝 Debugging Log Template

When asking for help, include:

```
Deployment Issue Report
======================
Error Message: [copy full error]
Steps to reproduce: [what did you do]
Expected behavior: [what should happen]
Actual behavior: [what happened instead]

Environment:
- DB_HOST: [value]
- DB_NAME: [value]
- Last deployment: [timestamp]
- Vercel logs: [paste relevant section]
```

---

## ✅ Verification Steps

Before concluding it's broken:

1. [ ] Checked Vercel logs
2. [ ] Verified all 5 environment variables
3. [ ] Tested database connection
4. [ ] Checked browser console
5. [ ] Hard refreshed browser (Ctrl+Shift+R)
6. [ ] Redeployed application
7. [ ] Waited 2-3 minutes for new deployment

---

**Last Updated:** November 19, 2025
