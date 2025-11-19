<?php
// Test script for users backend
session_start();

// Simulate admin session
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'Admin';
$_SESSION['user_name'] = 'Test Admin';

// Set the action
$_GET['action'] = 'getUsers';

// Include the database file first
require_once 'PHP/db.php';

// Include the backend file
require_once 'Admin/Users/back/users_backend.php';
?> 