<?php
/**
 * Email Configuration for Pili Beach Resort Contact Form
 * 
 * This file contains email settings and instructions for setting up
 * proper email delivery from your contact form.
 */

// Email Configuration Settings
$email_config = [
    // Your Gmail account where contact form submissions will be sent
    'recipient_email' => 'luisgabrielronatay@gmail.com',
    
    // From email address (should match your domain)
    'from_email' => 'noreply@pilibeachresort.com',
    
    // From name
    'from_name' => 'Pili Beach Resort',
    
    // Email subject prefix
    'subject_prefix' => 'New Contact Form Submission: ',
    
    // Backup file location (when email fails)
    'backup_file' => 'contact_backup_' . date('Y-m-d') . '.txt'
];

/**
 * IMPORTANT: To enable proper email delivery, you need to:
 * 
 * 1. Configure your server's mail settings:
 *    - For XAMPP: Edit php.ini and set SMTP settings
 *    - For hosting: Contact your hosting provider
 * 
 * 2. Alternative solutions:
 *    - Use a third-party email service (SendGrid, Mailgun, etc.)
 *    - Use Gmail SMTP with PHPMailer
 *    - Use a form service like Formspree or Netlify Forms
 * 
 * 3. For Gmail SMTP setup:
 *    - Enable 2-factor authentication on your Gmail account
 *    - Generate an App Password
 *    - Use PHPMailer library for SMTP authentication
 */

// Function to check if email is properly configured
function check_email_configuration() {
    // Check if mail() function is available
    if (!function_exists('mail')) {
        return [
            'status' => 'error',
            'message' => 'mail() function is not available on this server'
        ];
    }
    
    // Check if we can send a test email
    $test_result = mail(
        'test@example.com',
        'Test Email',
        'This is a test email',
        'From: noreply@pilibeachresort.com'
    );
    
    if ($test_result) {
        return [
            'status' => 'success',
            'message' => 'Email configuration appears to be working'
        ];
    } else {
        return [
            'status' => 'warning',
            'message' => 'Email sending failed. Check server configuration.'
        ];
    }
}

// Function to get email configuration
function get_email_config() {
    global $email_config;
    return $email_config;
}

// Function to send email with better error handling
function send_contact_email($to_email, $subject, $message, $from_email, $from_name) {
    $headers = array();
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=UTF-8';
    $headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
    $headers[] = 'Reply-To: ' . $from_email;
    $headers[] = 'X-Mailer: PHP/' . phpversion();
    
    $result = mail($to_email, $subject, $message, implode("\r\n", $headers));
    
    if (!$result) {
        error_log("Failed to send email to: " . $to_email);
        error_log("Email subject: " . $subject);
    }
    
    return $result;
}

// Uncomment the line below to test email configuration
// $test_result = check_email_configuration();
// echo "Email Test Result: " . $test_result['message'];
?> 