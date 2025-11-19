<?php

// Load environment variables if .env exists (for local development)
if (file_exists(__DIR__ . '/../.env')) {
    $envFile = __DIR__ . '/../.env';
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                putenv("$key=$value");
            }
        }
    }
}

// Get database configuration from environment variables or use defaults
$servername = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASSWORD') ?: "";
$dbname = getenv('DB_NAME') ?: "pili_db";
$dbport = getenv('DB_PORT') ?: "3306";

try {
    $conn = new PDO(
        "mysql:host=$servername;port=$dbport;dbname=$dbname",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 10
        ]
    );
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    error_log("Host: $servername, User: $username, DB: $dbname, Port: $dbport");
    die("Database connection failed. Please try again later.");
}

// Remove the problematic slave connection that was causing issues
?>