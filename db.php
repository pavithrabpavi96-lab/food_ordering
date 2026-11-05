<?php

$host = "YOUR_DATABASE_HOST";
$user = "YOUR_DATABASE_USERNAME";
$password = "YOUR_DATABASE_PASSWORD";
$database = "YOUR_DATABASE_NAME";
$port = 3306;

$conn = new mysqli(
    $host,
    $user,
    $password,
    $database,
    $port
);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

?>
