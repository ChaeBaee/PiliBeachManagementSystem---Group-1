# Gmail SMTP Setup Guide for Contact Form

## 🎯 Goal
Enable your contact form to send emails directly to your Gmail account (`luisgabrielronatay@gmail.com`) instead of just saving to backup files.

## 📋 Current Status
✅ Contact form is working and capturing messages  
✅ Backup system is saving messages safely  
❌ Email delivery is not configured (using backup system)

## 🔧 Setup Options

### Option 1: Quick Solution - Use Formspree (Recommended for now)

1. **Go to [Formspree.io](https://formspree.io)**
2. **Create a free account**
3. **Create a new form**
4. **Get your form endpoint URL**
5. **Update your contact form to use Formspree**

**Benefits:**
- No server configuration needed
- Works immediately
- Free for up to 50 submissions/month
- Professional email delivery

### Option 2: Gmail SMTP Setup (Advanced)

#### Step 1: Enable 2-Factor Authentication
1. Go to [myaccount.google.com](https://myaccount.google.com)
2. Click on "Security"
3. Enable "2-Step Verification"

#### Step 2: Generate App Password
1. Go to [myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords)
2. Select "Mail" and "Other (Custom name)"
3. Name it "Pili Beach Resort Contact Form"
4. Copy the generated 16-character password

#### Step 3: Update Email Configuration
1. Open `PHP/contact/back/contact_backend_gmail.php`
2. Replace `'your-app-password'` with your actual app password
3. Update the contact form to use this new backend

#### Step 4: Test Email Delivery
1. Submit a test contact form
2. Check your Gmail inbox
3. Check spam folder if not in inbox

### Option 3: XAMPP Mail Server Setup

#### Configure XAMPP for Email
1. **Install Mercury Mail Server:**
   - Download from [XAMPP website](https://www.apachefriends.org/download.html)
   - Install Mercury component

2. **Configure php.ini:**
   - Open `C:\xampp\php\php.ini`
   - Find and update these lines:
   ```ini
   [mail function]
   SMTP=localhost
   smtp_port=25
   sendmail_from = noreply@pilibeachresort.com
   ```

3. **Configure Mercury:**
   - Open Mercury Control Panel
   - Set up local mail server
   - Configure to forward to Gmail

## 🚀 Recommended Quick Fix

For immediate results, I recommend **Option 1 (Formspree)**:

1. **Sign up at Formspree.io**
2. **Create a new form**
3. **Replace your contact form action with Formspree URL**

Example:
```html
<form action="https://formspree.io/f/YOUR_FORM_ID" method="POST">
```

## 📊 Current Backup Files

Your contact form submissions are currently saved in:
- `PHP/contact_backup_2025-06-22.txt` (today's submissions)
- `PHP/contact_log.txt` (older submissions)

## 🔍 Testing

To test if email is working:
1. Run: `http://localhost/Proj/PHP/test_email.php`
2. Check the results
3. Look for test emails in your Gmail

## 📞 Alternative Solutions

If you prefer not to set up email:

1. **Check backup files daily** - All messages are saved safely
2. **Use a form service** - Formspree, Netlify Forms, Google Forms
3. **Set up notifications** - Get alerts when new backup files are created

## 🎉 Success Indicators

You'll know it's working when:
- ✅ Contact form shows "Thank you! Your message has been sent successfully to luisgabrielronatay@gmail.com"
- ✅ You receive emails in your Gmail inbox
- ✅ No more "Email delivery was temporarily unavailable" messages

## 🆘 Need Help?

If you need assistance with any of these options:
1. Try Option 1 (Formspree) first - it's the easiest
2. Check the test script results
3. Review the backup files to ensure messages aren't being lost

---

**Current Status:** ✅ Working with backup system  
**Next Step:** Choose and implement one of the email delivery options above 