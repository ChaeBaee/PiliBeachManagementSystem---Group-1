<?php
session_start();
require_once '../../../PHP/db.php';

$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$conn->exec("SET @current_user_id = $currentUserId");
header('Content-Type: application/json');

// CSRF Protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || $_SESSION['csrf_token'] !== $_POST['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Invalid request. Please refresh the page and try again.']);
        exit;
    }
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function isAdminLimitReached($conn)
{
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE user_role = 'Admin'");
    $stmt->execute();
    return $stmt->fetchColumn() >= 3;
}

function validatePhone($phone)
{
    // Philippine phone number validation (11 digits starting with 09)
    return preg_match('/^09[0-9]{9}$/', $phone);
}

function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) && 
           preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email);
}

function validatePassword($password)
{
    // Password must be at least 8 characters with at least one uppercase, one lowercase, one number
    return strlen($password) >= 8 && 
           preg_match('/[A-Z]/', $password) && 
           preg_match('/[a-z]/', $password) && 
           preg_match('/[0-9]/', $password);
}

function sanitizeInput($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function generateUsername($email)
{
    // Generate username from email (before @ symbol)
    $username = strtolower(explode('@', $email)[0]);
    
    // Check if username exists and append number if needed
    $baseUsername = $username;
    $counter = 1;
    
    while (true) {
        $stmt = $GLOBALS['conn']->prepare("SELECT COUNT(*) FROM users WHERE user_name = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() == 0) {
            break;
        }
        $username = $baseUsername . $counter;
        $counter++;
    }
    
    return $username;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'getUsers':
        try {
            // Add debugging
            error_log("getUsers action called");
            
            $search = $_GET['search'] ?? $_POST['search'] ?? '';
            $roleFilter = $_GET['role_filter'] ?? $_POST['role_filter'] ?? '';
            
            $query = "SELECT 
                        user_id, 
                        user_name, 
                        user_email, 
                        user_phone, 
                        user_role, 
                        user_date_created,
                        CASE 
                            WHEN user_role = 'Admin' THEN 'bg-red-100 text-red-800'
                            WHEN user_role = 'Guest' THEN 'bg-blue-100 text-blue-800'
                            ELSE 'bg-gray-100 text-gray-800'
                        END as role_class
                    FROM users 
                    WHERE 1=1";
            
            $params = [];
            
            // Search functionality
            if (!empty($search)) {
                $query .= " AND (user_name LIKE ? OR user_email LIKE ? OR user_phone LIKE ?)";
                $searchParam = "%$search%";
                $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
            }
            
            // Role filter
            if (!empty($roleFilter)) {
                $query .= " AND user_role = ?";
                $params[] = $roleFilter;
            }
            
            $query .= " ORDER BY user_date_created DESC";
            
            error_log("Query: " . $query);
            error_log("Params: " . print_r($params, true));
            
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Found " . count($users) . " users");
            
            echo json_encode($users);
        } catch (PDOException $e) {
            error_log("Database error in getUsers: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error fetching users: ' . $e->getMessage()]);
        }
        break;

    case 'add':
        try {
            // Validate required fields
            $requiredFields = ['userName', 'userEmail', 'userPassword', 'userPhone', 'userRole'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Field '$field' is required");
                }
            }

            // Sanitize inputs
            $userName = sanitizeInput($_POST['userName']);
            $userEmail = strtolower(sanitizeInput($_POST['userEmail']));
            $userPhone = sanitizeInput($_POST['userPhone']);
            $userRole = sanitizeInput($_POST['userRole']);
            $userPassword = $_POST['userPassword'];

            // Validate email format
            if (!validateEmail($userEmail)) {
                throw new Exception('Please enter a valid email address');
            }

            // Validate phone number
            if (!validatePhone($userPhone)) {
                throw new Exception('Phone number must be 11 digits starting with 09');
            }

            // Validate password strength
            if (!validatePassword($userPassword)) {
                throw new Exception('Password must be at least 8 characters with uppercase, lowercase, and number');
            }

            // Check if email already exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE user_email = ?");
            $stmt->execute([$userEmail]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Email address is already registered');
            }

            // Check if phone already exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE user_phone = ?");
            $stmt->execute([$userPhone]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Phone number is already registered');
            }

            // Validate role
            $allowedRoles = ['Guest', 'Admin'];
            if (!in_array($userRole, $allowedRoles)) {
                throw new Exception('Invalid user role selected');
            }

            // Check admin limit
            if ($userRole === 'Admin' && isAdminLimitReached($conn)) {
                throw new Exception('Maximum number of admin accounts (3) reached');
            }

            // Hash password
            $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);

            // Generate username if not provided
            if (empty($userName)) {
                $userName = generateUsername($userEmail);
            }

            // Insert user with transaction
            $conn->beginTransaction();
            
            try {
                $stmt = $conn->prepare("
                    INSERT INTO users (user_name, user_email, user_password, user_role, user_phone, user_date_created) 
                    VALUES (?, ?, ?, ?, ?, CURDATE())
                ");
                $stmt->execute([
                    $userName,
                    $userEmail,
                    $hashedPassword,
                    $userRole,
                    $userPhone
                ]);

                $userId = $conn->lastInsertId();

                // Log the user creation
                $stmt = $conn->prepare("
                    INSERT INTO audit_logs (table_name, record_id, action, new_data, changed_by, timestamp) 
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $newData = json_encode([
                    'user_name' => $userName,
                    'user_email' => $userEmail,
                    'user_role' => $userRole,
                    'user_phone' => $userPhone,
                    'created_by' => $currentUserId
                ]);
                $stmt->execute(['users', $userId, 'INSERT', $newData, $currentUserId]);

                $conn->commit();

                echo json_encode([
                    'success' => true, 
                    'message' => 'User added successfully. User ID: ' . $userId,
                    'userId' => $userId
                ]);
            } catch (Exception $e) {
                $conn->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'edit':
        try {
            // Validate required fields
            if (empty($_POST['userId']) || empty($_POST['userName']) || empty($_POST['userPhone'])) {
                throw new Exception('Required fields are missing');
            }

            // Sanitize inputs
            $userId = intval($_POST['userId']);
            $userName = sanitizeInput($_POST['userName']);
            $userPhone = sanitizeInput($_POST['userPhone']);

            // Validate phone number
            if (!validatePhone($userPhone)) {
                throw new Exception('Phone number must be 11 digits starting with 09');
            }

            // Check if user exists
            $stmt = $conn->prepare("SELECT user_id, user_name, user_phone FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $existingUser = $stmt->fetch();
            
            if (!$existingUser) {
                throw new Exception('User not found');
            }

            // Check if phone is already used by another user
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE user_phone = ? AND user_id != ?");
            $stmt->execute([$userPhone, $userId]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Phone number is already registered by another user');
            }

            // Update user with transaction
            $conn->beginTransaction();
            
            try {
                $stmt = $conn->prepare("UPDATE users SET user_name = ?, user_phone = ? WHERE user_id = ?");
                $result = $stmt->execute([$userName, $userPhone, $userId]);

                if (!$result) {
                    throw new Exception('Failed to update user');
                }

                // Log the update
                $stmt = $conn->prepare("
                    INSERT INTO audit_logs (table_name, record_id, action, old_data, new_data, changed_by, timestamp) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");
                $oldData = json_encode([
                    'user_name' => $existingUser['user_name'],
                    'user_phone' => $existingUser['user_phone']
                ]);
                $newData = json_encode([
                    'user_name' => $userName,
                    'user_phone' => $userPhone
                ]);
                $stmt->execute(['users', $userId, 'UPDATE', $oldData, $newData, $currentUserId]);

                $conn->commit();

                echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            } catch (Exception $e) {
                $conn->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'delete':
        try {
            if (empty($_POST['userId'])) {
                throw new Exception('User ID is required');
            }

            $userId = intval($_POST['userId']);

            // Check if user exists and get user info
            $stmt = $conn->prepare("SELECT user_id, user_name, user_role FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user) {
                throw new Exception('User not found');
            }

            // Prevent deletion of the last admin
            if ($user['user_role'] === 'Admin') {
                $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE user_role = 'Admin'");
                $stmt->execute();
                if ($stmt->fetchColumn() <= 1) {
                    throw new Exception('Cannot delete the last admin user');
                }
            }

            // Check if user has active bookings
            $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ? AND booking_status IN ('Confirmed', 'Pending')");
            $stmt->execute([$userId]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Cannot delete user with active bookings');
            }

            // Delete user with transaction
            $conn->beginTransaction();
            
            try {
                $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
                $result = $stmt->execute([$userId]);

                if (!$result) {
                    throw new Exception('Failed to delete user');
                }

                // Log the deletion
                $stmt = $conn->prepare("
                    INSERT INTO audit_logs (table_name, record_id, action, old_data, changed_by, timestamp) 
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $oldData = json_encode([
                    'user_name' => $user['user_name'],
                    'user_role' => $user['user_role']
                ]);
                $stmt->execute(['users', $userId, 'DELETE', $oldData, $currentUserId]);

                $conn->commit();

                echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            } catch (Exception $e) {
                $conn->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'getCSRFToken':
        echo json_encode(['csrf_token' => $_SESSION['csrf_token']]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}