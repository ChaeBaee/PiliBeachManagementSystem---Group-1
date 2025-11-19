<?php
session_start();
require_once '../../../PHP/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header('Location: /Proj/Admin/Dashboard/front/index.php');
    exit();
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : '';

// Pagination settings
$records_per_page = 25;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $records_per_page;

// Base query for counting total records
$count_query = "SELECT COUNT(*) as total 
                FROM audit_logs al 
                LEFT JOIN users u ON al.changed_by = u.user_id 
                WHERE 1=1";

$count_params = [];

if (!empty($search)) {
    $count_query .= " AND (al.table_name LIKE ? OR al.action LIKE ? OR u.user_name LIKE ?)";
    $search_param = "%$search%";
    $count_params = array_merge($count_params, [$search_param, $search_param, $search_param]);
}

if (!empty($date_filter)) {
    $count_query .= " AND DATE(al.changed_at) = ?";
    $count_params[] = $date_filter;
}

// Get total count
$count_stmt = $conn->prepare($count_query);
$count_stmt->execute($count_params);
$total_records = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_records / $records_per_page);

// Main query with pagination
$query = "SELECT al.*, u.user_name as changed_by_name 
          FROM audit_logs al 
          LEFT JOIN users u ON al.changed_by = u.user_id 
          WHERE 1=1";

$params = [];

if (!empty($search)) {
    $query .= " AND (al.table_name LIKE ? OR al.action LIKE ? OR u.user_name LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
}

if (!empty($date_filter)) {
    $query .= " AND DATE(al.changed_at) = ?";
    $params[] = $date_filter;
}

$query .= " ORDER BY al.changed_at DESC LIMIT ? OFFSET ?";
$params[] = $records_per_page;
$params[] = $offset;

$stmt = $conn->prepare($query);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>