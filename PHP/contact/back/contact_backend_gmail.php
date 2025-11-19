<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../front/contact.php?status=error');
    exit();
}

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate email
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate phone number (basic validation)
function is_valid_phone($phone) {
    // Remove all non-digit characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    // Check if it has at least 10 digits
    return strlen($phone) >= 10;
}

// Function to send email using Gmail SMTP
function send_email_via_gmail_smtp($to_email, $subject, $message, $from_email, $from_name) {
    // Gmail SMTP Configuration
    // IMPORTANT: You need to set up Gmail App Password
    // 1. Enable 2-factor authentication on your Gmail account
    // 2. Generate an App Password: https://myaccount.google.com/apppasswords
    // 3. Replace 'your-app-password' below with your actual app password
    
    $smtp_host = 'smtp.gmail.com';
    $smtp_port = 587;
    $smtp_username = 'luisgabrielronatay@gmail.com'; // Your Gmail address
    $smtp_password = 'tlxd awno eelw btim'; // Replace with your Gmail app password
    
    // Email headers
    $headers = array();
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=UTF-8';
    $headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
    $headers[] = 'Reply-To: ' . $from_email;
    $headers[] = 'X-Mailer: PHP/' . phpversion();
    
    // For now, use the basic mail() function
    // In production, you should use PHPMailer or similar library
    $mail_sent = mail($to_email, $subject, $message, implode("\r\n", $headers));
    
    return $mail_sent;
}

// Function to save contact form data to a backup file
function save_contact_to_backup($data) {
    $backup_file = '../../contact_backup_' . date('Y-m-d') . '.txt';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[{$timestamp}] Contact Form Submission:\n";
    $log_entry .= "Name: {$data['name']}\n";
    $log_entry .= "Email: {$data['email']}\n";
    $log_entry .= "Phone: {$data['phone']}\n";
    $log_entry .= "Subject: {$data['subject']}\n";
    $log_entry .= "Message: {$data['message']}\n";
    $log_entry .= "----------------------------------------\n\n";
    
    file_put_contents($backup_file, $log_entry, FILE_APPEND | LOCK_EX);
    return true;
}

try {
    // Get and sanitize form data
    $first_name = sanitize_input($_POST['first_name'] ?? '');
    $last_name = sanitize_input($_POST['last_name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $subject = sanitize_input($_POST['subject'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');
    $privacy = isset($_POST['privacy']) ? true : false;

    // Validation
    $errors = [];

    // Check required fields
    if (empty($first_name)) {
        $errors[] = "First name is required";
    }
    if (empty($last_name)) {
        $errors[] = "Last name is required";
    }
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!is_valid_email($email)) {
        $errors[] = "Invalid email format";
    }
    if (empty($subject)) {
        $errors[] = "Subject is required";
    }
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    if (!$privacy) {
        $errors[] = "Privacy policy agreement is required";
    }

    // Validate phone if provided
    if (!empty($phone) && !is_valid_phone($phone)) {
        $errors[] = "Invalid phone number format";
    }

    // If there are validation errors, redirect back with error
    if (!empty($errors)) {
        $_SESSION['contact_errors'] = $errors;
        header('Location: ../front/contact.php?status=error');
        exit();
    }

    // Prepare email content
    $full_name = $first_name . ' ' . $last_name;
    $email_subject = "New Contact Form Submission: " . $subject;
    
    // Email body
    $email_body = "
    <html>
    <head>
        <title>New Contact Form Submission</title>
    </head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='color: #3b82f6; border-bottom: 2px solid #3b82f6; padding-bottom: 10px;'>
                New Contact Form Submission
            </h2>
            
            <div style='background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3 style='color: #333; margin-top: 0;'>Contact Information</h3>
                <p><strong>Name:</strong> {$full_name}</p>
                <p><strong>Email:</strong> {$email}</p>
                " . (!empty($phone) ? "<p><strong>Phone:</strong> {$phone}</p>" : "") . "
                <p><strong>Subject:</strong> {$subject}</p>
                <p><strong>Date:</strong> " . date('F j, Y \a\t g:i A') . "</p>
            </div>
            
            <div style='background-color: #fff; padding: 20px; border: 1px solid #e9ecef; border-radius: 8px;'>
                <h3 style='color: #333; margin-top: 0;'>Message</h3>
                <p style='white-space: pre-wrap;'>{$message}</p>
            </div>
            
            <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef; font-size: 12px; color: #6c757d;'>
                <p>This message was sent from the contact form on Pili Beach Resort website.</p>
                <p>Please respond to: {$email}</p>
            </div>
        </div>
    </body>
    </html>
    ";

    // Recipient email - Your Gmail account
    $to_email = 'luisgabrielronatay@gmail.com';

    // Try to send email using Gmail SMTP
    $mail_sent = send_email_via_gmail_smtp($to_email, $email_subject, $email_body, 'noreply@pilibeachresort.com', 'Pili Beach Resort');

    if ($mail_sent) {
        // Log the contact submission to database (optional)
        log_contact_submission($full_name, $email, $phone, $subject, $message);
        
        // Redirect with success message
        header('Location: ../front/contact.php?status=success');
        exit();
    } else {
        // If email fails, save to backup file and show success message
        save_contact_to_backup([
            'name' => $full_name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message
        ]);
        
        // Log the contact submission to database (optional)
        log_contact_submission($full_name, $email, $phone, $subject, $message);
        
        // Redirect with success message but note backup was used
        header('Location: ../front/contact.php?status=success&backup=true');
        exit();
    }

} catch (Exception $e) {
    // Log error for debugging
    error_log("Contact form error: " . $e->getMessage());
    
    // Try to save the data anyway
    if (isset($full_name, $email, $phone, $subject, $message)) {
        save_contact_to_backup([
            'name' => $full_name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message
        ]);
    }
    
    // Redirect with error message
    header('Location: ../front/contact.php?status=error');
    exit();
}

// Function to log contact submissions to database (optional)
function log_contact_submission($name, $email, $phone, $subject, $message) {
    try {
        // Include database connection
        require_once '../../db.php';
        
        // Prepare SQL statement
        $sql = "INSERT INTO contact_submissions (name, email, phone, subject, message, created_at) 
                VALUES (:name, :email, :phone, :subject, :message, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':message', $message);
        
        $stmt->execute();
        
    } catch (PDOException $e) {
        // Log database error but don't fail the email sending
        error_log("Database error in contact form: " . $e->getMessage());
    }
}
?> 