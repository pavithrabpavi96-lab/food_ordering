<?php
session_start();

$host = getenv("DB_HOST");
$user = getenv("DB_USER");
$password = getenv("DB_PASSWORD");
$database = getenv("DB_NAME");
$port = (int)(getenv("DB_PORT") ?: 3306);

$conn = new mysqli(
    $host,
    $user,
    $password,
    $database,
    $port
);

if ($conn->connect_error) {
    error_log("Database connection failed: " .
        $conn->connect_error);
    http_response_code(500);
    exit("Database connection error.");
}

$conn->set_charset("utf8mb4");

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}
?>