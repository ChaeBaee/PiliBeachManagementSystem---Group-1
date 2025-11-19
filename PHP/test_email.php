<?php
/**
 * Email Test Script for Pili Beach Resort
 * 
 * This script tests if email functionality is working on your server.
 * Run this file in your browser to check email configuration.
 */

// Include email configuration
require_once 'email_config.php';

echo "<h1>Email Configuration Test</h1>";
echo "<p>Testing email functionality for Pili Beach Resort contact form...</p>";

// Test 1: Check if mail() function exists
echo "<h2>Test 1: Mail Function Availability</h2>";
if (function_exists('mail')) {
    echo "<p style='color: green;'>✓ mail() function is available</p>";
} else {
    echo "<p style='color: red;'>✗ mail() function is NOT available</p>";
    echo "<p>This means your server doesn't support PHP mail functionality.</p>";
}

// Test 2: Check email configuration
echo "<h2>Test 2: Email Configuration</h2>";
$config = get_email_config();
echo "<p><strong>Recipient Email:</strong> " . $config['recipient_email'] . "</p>";
echo "<p><strong>From Email:</strong> " . $config['from_email'] . "</p>";
echo "<p><strong>From Name:</strong> " . $config['from_name'] . "</p>";

// Test 3: Try to send a test email
echo "<h2>Test 3: Email Sending Test</h2>";
echo "<p>Attempting to send a test email...</p>";

$test_subject = "Test Email from Pili Beach Resort Website";
$test_message = "
<html>
<body>
<h2>Test Email</h2>
<p>This is a test email from your Pili Beach Resort website.</p>
<p>If you receive this email, your contact form should work properly.</p>
<p>Sent at: " . date('Y-m-d H:i:s') . "</p>
</body>
</html>";

$headers = array();
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=UTF-8';
$headers[] = 'From: ' . $config['from_name'] . ' <' . $config['from_email'] . '>';
$headers[] = 'X-Mailer: PHP/' . phpversion();

$mail_sent = mail($config['recipient_email'], $test_subject, $test_message, implode("\r\n", $headers));

if ($mail_sent) {
    echo "<p style='color: green;'>✓ Test email sent successfully!</p>";
    echo "<p>Check your Gmail inbox (and spam folder) for the test email.</p>";
} else {
    echo "<p style='color: red;'>✗ Test email failed to send</p>";
    echo "<p>This means your server's email configuration needs to be set up.</p>";
}

// Test 4: Check backup functionality
echo "<h2>Test 4: Backup System</h2>";
$backup_file = 'contact_backup_' . date('Y-m-d') . '.txt';
$test_backup_content = "[TEST] Contact Form Backup Test\n";
$test_backup_content .= "Date: " . date('Y-m-d H:i:s') . "\n";
$test_backup_content .= "This is a test backup entry.\n";
$test_backup_content .= "----------------------------------------\n\n";

$backup_saved = file_put_contents($backup_file, $test_backup_content, FILE_APPEND | LOCK_EX);

if ($backup_saved !== false) {
    echo "<p style='color: green;'>✓ Backup system is working</p>";
    echo "<p>Backup file: " . $backup_file . "</p>";
} else {
    echo "<p style='color: red;'>✗ Backup system failed</p>";
}

// Recommendations
echo "<h2>Recommendations</h2>";
if (!$mail_sent) {
    echo "<div style='background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px;'>";
    echo "<h3>Email Not Working? Here are your options:</h3>";
    echo "<ol>";
    echo "<li><strong>For XAMPP/Local Development:</strong>";
    echo "<ul>";
    echo "<li>Edit your php.ini file and configure SMTP settings</li>";
    echo "<li>Install and configure a local mail server like Mercury</li>";
    echo "<li>Use a service like Mailtrap for testing</li>";
    echo "</ul></li>";
    echo "<li><strong>For Production/Hosting:</strong>";
    echo "<ul>";
    echo "<li>Contact your hosting provider to enable email functionality</li>";
    echo "<li>Use a third-party email service (SendGrid, Mailgun, etc.)</li>";
    echo "<li>Use Gmail SMTP with PHPMailer library</li>";
    echo "</ul></li>";
    echo "<li><strong>Alternative Solutions:</strong>";
    echo "<ul>";
    echo "<li>Use a form service like Formspree, Netlify Forms, or Google Forms</li>";
    echo "<li>Set up a webhook to send data to your email</li>";
    echo "<li>Use a CRM system that can receive form submissions</li>";
    echo "</ul></li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<p style='color: green;'>🎉 Great! Your email system is working properly!</p>";
    echo "<p>Your contact form should now send emails to your Gmail account.</p>";
}

echo "<h2>Current Status</h2>";
echo "<p><strong>Contact Form:</strong> ";
if ($mail_sent) {
    echo "<span style='color: green;'>✓ Working - emails will be sent to your Gmail</span>";
} else {
    echo "<span style='color: orange;'>⚠ Backup mode - messages saved to backup files</span>";
}
echo "</p>";

echo "<p><strong>Backup System:</strong> ";
if ($backup_saved !== false) {
    echo "<span style='color: green;'>✓ Working - messages saved when email fails</span>";
} else {
    echo "<span style='color: red;'>✗ Not working</span>";
}
echo "</p>";

echo "<hr>";
echo "<p><small>Test completed at: " . date('Y-m-d H:i:s') . "</small></p>";
?> 