<?php
session_start();
require_once '../../../PHP/db.php';

$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$conn->exec("SET @current_user_id = $currentUserId");
header('Content-Type: application/json');


function isAdminLimitReached($conn)
{
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE user_role = 'Admin'");
    $stmt->execute();
    return $stmt->fetchColumn() >= 3;
}


function validatePhone($phone)
{
    return preg_match('/^[0-9]{11}$/', $phone);
}


$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'getUsers':
        try {
            $stmt = $conn->prepare("SELECT user_id, user_name, user_email, user_phone, user_role, user_date_created FROM users");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($users);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error fetching users: ' . $e->getMessage()]);
        }
        break;

    case 'add':
        try {

            if (empty($_POST['userName']) || empty($_POST['userEmail']) || empty($_POST['userPassword']) || empty($_POST['userPhone'])) {
                throw new Exception('All fields are required');
            }

            if (!validatePhone($_POST['userPhone'])) {
                throw new Exception('Phone number must be 11 digits');
            }

            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE user_email = ?");
            $stmt->execute([$_POST['userEmail']]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Email already exists');
            }

            if ($_POST['userRole'] === 'Admin' && isAdminLimitReached($conn)) {
                throw new Exception('Maximum number of admin accounts (3) reached');
            }

            $hashedPassword = password_hash($_POST['userPassword'], PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (user_name, user_email, user_password, user_role, user_phone, user_date_created) VALUES (?, ?, ?, ?, ?, CURDATE())");
            $stmt->execute([
                $_POST['userName'],
                $_POST['userEmail'],
                $hashedPassword,
                $_POST['userRole'],
                $_POST['userPhone']
            ]);

            echo json_encode(['success' => true, 'message' => 'User added successfully']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'edit':

        try {

            if (empty($_POST['userId']) || empty($_POST['userName']) || empty($_POST['userPhone'])) {
                throw new Exception('Required fields are missing');
            }


            if (!validatePhone($_POST['userPhone'])) {
                throw new Exception('Phone number must be 11 digits');
            }


            $stmt = $conn->prepare("UPDATE users SET user_name = ?, user_phone = ? WHERE user_id = ?");
            $result = $stmt->execute([
                $_POST['userName'],
                $_POST['userPhone'],
                $_POST['userId']
            ]);

            if (!$result) {
                throw new Exception('Failed to update user');
            }

            echo json_encode(['success' => true, 'message' => 'User updated successfully']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'delete':

        try {
            if (empty($_POST['userId'])) {
                throw new Exception('User ID is required');
            }

            $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
            $result = $stmt->execute([$_POST['userId']]);

            if (!$result) {
                throw new Exception('Failed to delete user');
            }

            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}