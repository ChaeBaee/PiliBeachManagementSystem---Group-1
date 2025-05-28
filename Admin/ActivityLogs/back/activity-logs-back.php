<?php
session_start();
require_once '../../../PHP/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header('Location: login.php');
    exit();
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : '';

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

$query .= " ORDER BY al.changed_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>