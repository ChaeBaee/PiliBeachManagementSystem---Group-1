<?php
/**
 * Health Check Endpoint
 * Used by Vercel to verify deployment status
 */

require_once __DIR__ . '/../PHP/db.php';

$health = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'database' => 'checking...'
];

try {
    $result = $conn->query('SELECT 1');
    $health['database'] = 'connected';
} catch (Exception $e) {
    $health['database'] = 'error: ' . $e->getMessage();
    $health['status'] = 'error';
}

header('Content-Type: application/json');
echo json_encode($health, JSON_PRETTY_PRINT);
