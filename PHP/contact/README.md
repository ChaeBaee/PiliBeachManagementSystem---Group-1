# Contact Form - Pili Beach Resort

This contact form allows visitors to send messages directly to the resort's email address.

## Features

- **Modern, Responsive Design**: Built with Tailwind CSS for a beautiful, mobile-friendly interface
- **Form Validation**: Client-side and server-side validation for all fields
- **Email Integration**: Sends formatted HTML emails to the resort
- **Database Logging**: Optional logging of contact submissions to database
- **Security**: Input sanitization and validation to prevent spam and attacks
- **User Feedback**: Success/error messages after form submission

## Files Structure

```
contact/
├── front/
│   └── contact.php          # Main contact form page
├── back/
│   └── contact_backend.php  # Form processing and email sending
├── contact_submissions.sql  # Database table creation script
└── README.md               # This file
```

## Setup Instructions

### 1. Database Setup

Run the SQL script to create the contact_submissions table:

```sql
-- Import contact_submissions.sql into your database
-- This creates a table to log contact form submissions
```

### 2. Email Configuration

Update the email settings in `back/contact_backend.php`:

```php
// Change this to your actual email address
$to_email = 'your-email@example.com';

// Update the from email if needed
$headers[] = 'From: Pili Beach Resort <noreply@yourdomain.com>';
```

### 3. Server Email Configuration

For the email functionality to work, ensure your server has:

- **PHP mail() function enabled**
- **SMTP server configured** (if using local development)
- **Proper DNS records** for your domain

### 4. Local Development Setup

If testing locally, you may need to:

1. **Configure XAMPP/WAMP mail settings**
2. **Use a service like Mailtrap** for testing
3. **Install and configure SMTP server**

## Configuration Options

### Email Template Customization

The email template is in HTML format and can be customized in `contact_backend.php`:

```php
$email_body = "
<html>
<head>
    <title>New Contact Form Submission</title>
</head>
<body>
    <!-- Customize the email content here -->
</body>
</html>
";
```

### Form Fields

The contact form includes:

- **First Name** (required)
- **Last Name** (required)
- **Email Address** (required)
- **Phone Number** (optional)
- **Subject** (required, dropdown selection)
- **Message** (required)
- **Privacy Policy Agreement** (required)

### Subject Options

Available subject options:
- General Inquiry
- Booking Question
- Room Information
- Pricing Inquiry
- Special Request
- Complaint
- Feedback
- Other

## Security Features

- **Input Sanitization**: All user inputs are cleaned and validated
- **Email Validation**: Proper email format validation
- **Phone Validation**: Basic phone number format checking
- **CSRF Protection**: Form validation and session handling
- **SQL Injection Prevention**: Prepared statements for database operations

## Customization

### Styling

The form uses Tailwind CSS classes. You can customize the appearance by:

1. Modifying the CSS classes in `contact.php`
2. Adding custom CSS in the `<style>` section
3. Updating the color scheme to match your brand

### Form Fields

To add/remove form fields:

1. Update the HTML form in `contact.php`
2. Modify the validation in `contact_backend.php`
3. Update the email template
4. Modify the database table if logging is enabled

### Email Content

The email includes:
- Contact information (name, email, phone)
- Subject and message
- Timestamp
- Professional formatting

## Troubleshooting

### Common Issues

1. **Emails not sending**: Check server mail configuration
2. **Form not submitting**: Verify file paths and permissions
3. **Database errors**: Ensure table exists and connection is working
4. **Validation errors**: Check required fields and format validation

### Debug Mode

To enable debug mode, add this to `contact_backend.php`:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Browser Compatibility

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## Dependencies

- PHP 7.4+
- MySQL/MariaDB
- Tailwind CSS (CDN)
- Font Awesome (CDN)
- Google Fonts (Inter)

## Support

For issues or questions:
1. Check the error logs
2. Verify server configuration
3. Test email functionality
4. Review form validation

## License

This contact form is part of the Pili Beach Resort project. 